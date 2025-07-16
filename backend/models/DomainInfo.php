<?php
namespace App\Models;

class DomainInfo {
    private $apiKey = 'f16cf0dfaa16dbc5ea97343d9bd774bd';
    private $apiUrl = 'http://api.whoapi.com/';
    
    public function getDomainInfo($url) {
        try {
            // Extract domain from URL
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            
            if (empty($domain)) {
                error_log("DomainInfo: Empty domain for URL: " . $url);
                return null;
            }
            
            // Build API URL with proper parameters
            $apiEndpoint = $this->apiUrl . '?' . http_build_query([
                'apikey' => $this->apiKey,
                'r' => 'whois',
                'domain' => $domain,
                'ip' => gethostbyname($domain)
            ]);
            
            error_log("Calling WhoAPI: " . $apiEndpoint);
            
            // Make API request
            $response = @file_get_contents($apiEndpoint);
            
            if ($response === false) {
                error_log("DomainInfo: API request failed for domain: " . $domain);
                return null;
            }
            
            $data = json_decode($response, true);
            error_log("WhoAPI Response: " . print_r($data, true));
            
            if (!$data || $data['status'] !== '0') {
                error_log("DomainInfo: Invalid API response for domain: " . $domain);
                return null;
            }
            
            // Calculate domain age in days
            $creationDate = isset($data['date_created']) ? strtotime($data['date_created']) : false;
            $domainAge = $creationDate ? floor((time() - $creationDate) / (60 * 60 * 24)) : 'Unknown';
            
            // Format domain status for better readability
            $domainStatus = [];
            if (isset($data['domain_status'])) {
                $domainStatus = is_array($data['domain_status']) 
                    ? array_slice($data['domain_status'], 0, 3) 
                    : [$data['domain_status']];
            } else {
                $domainStatus = ['Status information unavailable'];
            }
            
            // Extract contacts information
            $registrar = '';
            $owner = '';
            if (!empty($data['contacts']) && is_array($data['contacts'])) {
                foreach ($data['contacts'] as $contact) {
                    if ($contact['type'] === 'registrar') {
                        // Check both organization and name fields
                        $registrar = !empty($contact['organization']) ? $contact['organization'] : 
                                   (!empty($contact['name']) ? $contact['name'] : 'Unknown');
                    }
                    if ($contact['type'] === 'registrant') {
                        // Check both organization and name fields
                        $owner = !empty($contact['organization']) ? $contact['organization'] : 
                               (!empty($contact['name']) ? $contact['name'] : 'Private Registration');
                    }
                }
            }
            
            // Add error logging
            error_log("Registrar Info: " . print_r($registrar, true));
            error_log("Owner Info: " . print_r($owner, true));
            
            // Update the nameservers and emails handling
            $nameservers = [];
            if (!empty($data['nameservers']) && is_array($data['nameservers'])) {
                $nameservers = $data['nameservers'];
            } elseif (!empty($data['nameservers']) && is_string($data['nameservers'])) {
                $nameservers = explode(',', $data['nameservers']);
            }

            $emails = [];
            if (!empty($data['emails']) && is_array($data['emails'])) {
                foreach ($data['emails'] as $email) {
                    if (strpos($email, '*') === false) { // Only add non-redacted emails
                        $emails[] = $email;
                    }
                }
            }
            
            // Build comprehensive domain info with safe defaults
            return [
                'is_registered' => $data['registered'] ?? false,
                'domain_age' => is_numeric($domainAge) ? $domainAge . ' days' : 'Unknown',
                'creation_date' => $data['date_created'] ?? 'Unknown',
                'expiration_date' => $data['date_expires'] ?? 'Unknown',
                'last_updated' => $data['date_updated'] ?? 'Unknown',
                'status' => $domainStatus,
                'registrar' => $registrar ?: 'Unknown',
                'owner' => $owner ?: 'Private Registration',
                'whois_server' => $data['whois_server'] ?? 'Unknown',
                'nameservers' => !empty($nameservers) ? implode(', ', $nameservers) : 'Not Available',
                'domain_id' => $data['registry_domain_id'] ?? 'Unknown',
                'emails' => !empty($emails) ? implode(', ', $emails) : 'Private',
                'premium' => $data['premium'] ?? false,
                'status_details' => [
                    'description' => $data['status_desc'] ?? '',
                    'reason' => $data['domain_status_reason'] ?? ''
                ]
            ];
            
        } catch (\Exception $e) {
            error_log("DomainInfo Error: " . $e->getMessage());
            return null;
        }
    }
} 
<?php
namespace App\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhoisLibrary {
    private $client;
    
    public function __construct() {
        $this->client = new Client([
            'timeout' => 15,
            'verify' => false
        ]);
    }
    
    /**
     * Get WHOIS data using multiple free APIs
     */
    public function getWhoisData($domain) {
        // Try multiple free WHOIS APIs
        $methods = [
            'getWhoisFromWhoisXMLAPI',
            'getWhoisFromIPAPI',
            'getWhoisFromWhoisCom',
            'getWhoisFromDomainTools'
        ];
        
        foreach ($methods as $method) {
            try {
                $whoisData = $this->$method($domain);
                if ($whoisData && $this->isValidWhoisData($whoisData)) {
                    return $whoisData;
                }
            } catch (\Exception $e) {
                error_log("WHOIS method $method failed: " . $e->getMessage());
                continue;
            }
        }
        
        return null;
    }
    
    /**
     * Get WHOIS data from WhoisXMLAPI (free tier)
     */
    private function getWhoisFromWhoisXMLAPI($domain) {
        $url = "https://whois.whoisxmlapi.com/api/v1?domainName={$domain}&outputFormat=json";
        
        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);
            
            if (!$data || !isset($data['whoisRecord'])) {
                return null;
            }
            
            $whoisRecord = $data['whoisRecord'];
            
            return [
                'Domain Age' => $this->calculateDomainAge($whoisRecord['creationDate'] ?? null),
                'Domain Status' => $this->formatDomainStatus($whoisRecord['status'] ?? []),
                'Domain Registrar' => $whoisRecord['registrar'] ?? 'Unknown',
                'Domain Expiry' => $this->formatDate($whoisRecord['expiresDate'] ?? null),
                'Last Updated' => $this->formatDate($whoisRecord['updatedDate'] ?? null),
                'Nameservers' => $this->formatNameservers($whoisRecord['nameServers'] ?? [])
            ];
        } catch (RequestException $e) {
            error_log("WhoisXMLAPI error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get WHOIS data from IP-API (free)
     */
    private function getWhoisFromIPAPI($domain) {
        $ip = gethostbyname($domain);
        if ($ip === $domain) {
            return null;
        }
        
        $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,asname,query";
        
        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);
            
            if (!$data || $data['status'] !== 'success') {
                return null;
            }
            
            return [
                'Domain Age' => 'Unknown',
                'Domain Status' => 'Active',
                'Domain Registrar' => $data['isp'] ?? 'Unknown',
                'Domain Expiry' => 'Unknown',
                'Last Updated' => 'Unknown',
                'Nameservers' => $data['asname'] ?? 'Unknown'
            ];
        } catch (RequestException $e) {
            error_log("IP-API error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get WHOIS data from whois.com (free)
     */
    private function getWhoisFromWhoisCom($domain) {
        $url = "https://www.whois.com/whois/{$domain}";
        
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $html = $response->getBody();
            return $this->parseWhoisHTML($html);
        } catch (RequestException $e) {
            error_log("Whois.com error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get WHOIS data from DomainTools (free)
     */
    private function getWhoisFromDomainTools($domain) {
        $url = "https://whois.domaintools.com/{$domain}";
        
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $html = $response->getBody();
            return $this->parseWhoisHTML($html);
        } catch (RequestException $e) {
            error_log("DomainTools error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Parse WHOIS data from HTML
     */
    private function parseWhoisHTML($html) {
        $whoisData = [
            'Domain Age' => 'Unknown',
            'Domain Status' => 'Active',
            'Domain Registrar' => 'Unknown',
            'Domain Expiry' => 'Unknown',
            'Last Updated' => 'Unknown',
            'Nameservers' => 'Unknown'
        ];
        
        // Remove HTML tags and clean the response
        $cleanHtml = strip_tags($html);
        $cleanHtml = preg_replace('/\s+/', ' ', $cleanHtml);
        
        // Extract creation date
        if (preg_match('/Creation Date:\s*([^\n\r]+)/i', $cleanHtml, $matches)) {
            $whoisData['Domain Age'] = $this->calculateDomainAge(trim($matches[1]));
        }
        
        // Extract registrar
        if (preg_match('/Registrar:\s*([^\n\r]+)/i', $cleanHtml, $matches)) {
            $whoisData['Domain Registrar'] = trim($matches[1]);
        }
        
        // Extract expiry date
        if (preg_match('/Registry Expiry Date:\s*([^\n\r]+)/i', $cleanHtml, $matches)) {
            $whoisData['Domain Expiry'] = $this->formatDate(trim($matches[1]));
        }
        
        // Extract updated date
        if (preg_match('/Updated Date:\s*([^\n\r]+)/i', $cleanHtml, $matches)) {
            $whoisData['Last Updated'] = $this->formatDate(trim($matches[1]));
        }
        
        // Extract nameservers
        if (preg_match('/Name Server:\s*([^\n\r]+)/i', $cleanHtml, $matches)) {
            $whoisData['Nameservers'] = trim($matches[1]);
        }
        
        return $whoisData;
    }
    
    /**
     * Calculate domain age from creation date
     */
    private function calculateDomainAge($creationDate) {
        if (!$creationDate) return 'Unknown';
        
        try {
            $creation = new \DateTime($creationDate);
            $now = new \DateTime();
            $diff = $now->diff($creation);
            
            if ($diff->y > 0) {
                return $diff->y . ' year' . ($diff->y != 1 ? 's' : '') . ' ' . $diff->m . ' month' . ($diff->m != 1 ? 's' : '');
            } elseif ($diff->m > 0) {
                return $diff->m . ' month' . ($diff->m != 1 ? 's' : '') . ' ' . $diff->d . ' day' . ($diff->d != 1 ? 's' : '');
            } else {
                return $diff->d . ' day' . ($diff->d != 1 ? 's' : '');
            }
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    /**
     * Format domain status
     */
    private function formatDomainStatus($status) {
        if (empty($status)) return 'Unknown';
        
        if (is_array($status)) {
            return implode(', ', array_slice($status, 0, 3));
        }
        
        return $status;
    }
    
    /**
     * Format date
     */
    private function formatDate($date) {
        if (!$date) return 'Unknown';
        
        try {
            $dateObj = new \DateTime($date);
            return $dateObj->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    /**
     * Format nameservers
     */
    private function formatNameservers($nameservers) {
        if (empty($nameservers)) return 'Unknown';
        
        if (is_array($nameservers)) {
            return implode(', ', array_slice($nameservers, 0, 5));
        }
        
        return $nameservers;
    }
    
    /**
     * Validate WHOIS data
     */
    private function isValidWhoisData($whoisData) {
        $validFields = 0;
        $requiredFields = ['Domain Registrar', 'Domain Age', 'Domain Expiry', 'Last Updated'];
        
        foreach ($whoisData as $key => $value) {
            if ($value !== 'Unknown' && $value !== null && $value !== '' && $value !== 'N/A') {
                $validFields++;
            }
        }
        
        $requiredValid = 0;
        foreach ($requiredFields as $field) {
            if (isset($whoisData[$field]) && $whoisData[$field] !== 'Unknown' && $whoisData[$field] !== null && $whoisData[$field] !== '' && $whoisData[$field] !== 'N/A') {
                $requiredValid++;
            }
        }
        
        return ($validFields >= 3) && ($requiredValid >= 2);
    }
} 
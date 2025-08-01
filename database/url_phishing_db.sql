-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2025 at 11:27 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `url_phishing_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `daily_limit` int(11) DEFAULT 100,
  `requests_today` int(11) DEFAULT 0,
  `last_reset_date` date DEFAULT NULL,
  `last_used` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_blacklist`
--

CREATE TABLE `domain_blacklist` (
  `id` int(11) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domain_blacklist`
--

INSERT INTO `domain_blacklist` (`id`, `domain`, `reason`, `added_by`, `added_at`) VALUES
(1, 'allegro.oferta7321175.icu', 'Added from scan result', 9, '2025-07-26 02:02:53'),
(6, 'dapptrustline.xyz', 'Detected as phishing by AI scan', 9, '2025-07-31 03:03:07'),
(8, 'alibaba-inc.kefu.helps.live', 'Detected as phishing by AI scan', 9, '2025-07-31 22:59:23'),
(9, 'banco-nacional.com', 'Auto-blacklisted: High confidence phishing detection (100%) - 2025-08-01 01:23:35', 9, '2025-07-31 23:23:35'),
(10, 'gaalerabet.bet', 'Auto-blacklisted: High confidence phishing detection (100%) - 2025-08-01 01:23:58', 9, '2025-07-31 23:23:58'),
(11, 'banco-nacional.com/login', 'Added from import scan', 9, '2025-07-31 23:24:25'),
(12, 'allegro.oferta10924512.pl', 'Auto-blacklisted: High confidence phishing detection (100%) - 2025-08-01 01:28:43', 9, '2025-07-31 23:28:43'),
(13, 'blaze-award.net', 'Auto-blacklisted: High confidence phishing detection (100%) - 2025-08-01 11:26:24', 9, '2025-08-01 09:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `domain_reports`
--

CREATE TABLE `domain_reports` (
  `id` int(11) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domain_reports`
--

INSERT INTO `domain_reports` (`id`, `domain`, `reported_by`, `reason`, `report_date`) VALUES
(205632, 'allegrolokalnie.oferta8211469.icu', 9, 'Added from scan result', '2025-07-25 12:07:28'),
(205633, 'allegro.oferta8211469.icu', 9, 'Added from scan result', '2025-07-25 13:44:35'),
(205634, 'www.myposaziendaleassistenza.com', 9, 'Added from scan result', '2025-07-25 15:11:03'),
(205635, 'allegro.oferta7321175.icu', 9, 'Added from scan result', '2025-07-26 02:02:52'),
(205636, 'tinnajane319.wixstudio.com', 9, 'Added from scan result', '2025-07-26 03:10:19'),
(205637, 'ttttttttrtcnv.nl', 9, 'Phishing: phishing', '2025-07-26 04:06:14'),
(205638, 'allegrolokalnie.pl-oferta8702097.sbs', 9, 'Phishing: phishing', '2025-07-27 04:41:51'),
(205639, 'allegrolokalnie.pl-oferta8702097.sbs', 9, 'Admin blacklisted', '2025-07-27 04:43:18'),
(205640, 'allegro.pl-kategorie78126373167215.shop', 9, 'Phishing: phishing', '2025-07-27 04:44:24'),
(205641, 'allegro.pl-kategorie78126373167215.shop', 9, 'Added from scan result', '2025-07-27 04:44:38'),
(205642, 'sinarmascoid.com', 9, 'Phishing: phishing', '2025-07-27 04:47:09'),
(205643, 'allegrolokalnie.pi-59282845.rest', 9, 'Phishing: phishing', '2025-07-27 04:47:47'),
(205644, 'allegro.pi-58217582.click', 9, 'Phishing: phishing', '2025-07-27 04:48:07'),
(205645, 'allegro.pl-92745835.cfd', 9, 'Phishing: phishing', '2025-07-27 04:48:28'),
(205646, 'dapptrustline.xyz/connect.html', 9, 'Admin blacklisted', '2025-07-31 02:51:35'),
(205647, 'dapptrustline.xyz', 9, 'Detected as phishing by AI scan', '2025-07-31 03:03:07'),
(205648, 'martrut-my.com', 9, 'Detected as phishing by AI scan', '2025-07-31 22:38:48'),
(205649, 'alibaba-inc.kefu.helps.live', 9, 'Detected as phishing by AI scan', '2025-07-31 22:59:23'),
(205650, 'banco-nacional.com/login', 9, 'Added from import scan', '2025-07-31 23:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `scanned_domains`
--

CREATE TABLE `scanned_domains` (
  `id` int(11) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `first_scan_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_scan_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_scans` int(11) DEFAULT 1,
  `phishing_count` int(11) DEFAULT 0,
  `safe_count` int(11) DEFAULT 0,
  `average_confidence_score` decimal(5,2) DEFAULT 0.00,
  `last_scan_result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`last_scan_result`)),
  `whois_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`whois_info`)),
  `domain_age` varchar(100) DEFAULT 'Unknown',
  `domain_registrar` varchar(255) DEFAULT 'Unknown',
  `domain_status` varchar(100) DEFAULT 'Unknown',
  `is_blacklisted` tinyint(1) DEFAULT 0,
  `blacklist_date` timestamp NULL DEFAULT NULL,
  `blacklist_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scanned_domains`
--

INSERT INTO `scanned_domains` (`id`, `domain`, `first_scan_date`, `last_scan_date`, `total_scans`, `phishing_count`, `safe_count`, `average_confidence_score`, `last_scan_result`, `whois_info`, `domain_age`, `domain_registrar`, `domain_status`, `is_blacklisted`, `blacklist_date`, `blacklist_reason`) VALUES
(1, 'alibaba-inc.kefu.helps.live', '2025-07-31 21:59:04', '2025-07-31 21:59:04', 1, 1, 0, 100.00, '{\"url\":\"https:\\/\\/alibaba-inc.kefu.helps.live\",\"domain\":\"alibaba-inc.kefu.helps.live\",\"is_phishing\":true,\"confidence_score\":100,\"phishing_result\":\"phishing\",\"features\":{\"URL Length\":35,\"Domain Length\":27,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":3,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"Yes\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"Yes\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"1891 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-05-27 10:12:04\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok, ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"Alibaba Cloud Computing Ltd. d\\/b\\/a HiChina (www.net.cn)\",\"Last Updated\":\"2025-05-26 06:15:47\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.nic.live\",\"Nameservers\":\"cetus.dnspod.net, washer.dnspod.net\",\"Domain ID\":\"4caf76141f6d49bc88e6af6e0a22dcff-DONUTS\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.8},\"whois_info\":{\"Domain Age\":\"10 years 1 month\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"},\"expert_analysis\":\"\\ud83d\\udea8 **HIGH RISK**: This URL has been identified as a potential phishing threat.\\n\\n**Confidence Level**: 100% (High confidence in phishing detection)\\n\\n**AI Analysis**: phishing\\n\\n**Recommendations**:\\n\\n\\u2022 Do not visit this URL\\n\\n\\u2022 Do not enter any personal information\\n\\n\\u2022 Report this URL to your IT security team\\n\\n\\u2022 Consider adding this domain to your blacklist\",\"scan_timestamp\":\"2025-08-01 00:59:04\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"10 years 1 month\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}', '10 years 1 month', 'Unknown', 'ACTIVE', 0, NULL, NULL),
(2, 'goldlakesfcu.com', '2025-07-31 22:11:55', '2025-07-31 22:11:55', 1, 0, 1, 40.00, '{\"url\":\"https:\\/\\/goldlakesfcu.com\",\"domain\":\"goldlakesfcu.com\",\"is_phishing\":false,\"confidence_score\":40,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":24,\"Domain Length\":16,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"389 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-07-07 13:03:21\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"Unknown\",\"Last Updated\":\"2025-07-31 20:48:04\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.PublicDomainRegistry.com\",\"Nameservers\":\"BLAIR.NS.CLOUDFLARE.COM, KANYE.NS.CLOUDFLARE.COM\",\"Domain ID\":\"2897125762_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.625},\"whois_info\":{\"Domain Age\":\"1 year 0 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"PDR Ltd. d\\/b\\/a PublicDomainRegistry.com\",\"Domain Expiry\":\"2026-07-07 13:03:21\",\"Last Updated\":\"2025-07-31 20:48:04\",\"Nameservers\":\"BLAIR.NS.CLOUDFLARE.COM, KANYE.NS.CLOUDFLARE.COM\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 40% (Low confidence in phishing detection)\\n\\n**AI Analysis**: safe\\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 01:11:55\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"1 year 0 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"PDR Ltd. d\\/b\\/a PublicDomainRegistry.com\",\"Domain Expiry\":\"2026-07-07 13:03:21\",\"Last Updated\":\"2025-07-31 20:48:04\",\"Nameservers\":\"BLAIR.NS.CLOUDFLARE.COM, KANYE.NS.CLOUDFLARE.COM\"}', '1 year 0 months', 'PDR Ltd. d/b/a PublicDomainRegistry.com', 'clientTransferProhibited https://icann.org/epp#clientTransferProhibited', 0, NULL, NULL),
(3, 'vaquinhabr.com.br', '2025-07-31 22:12:55', '2025-07-31 22:28:38', 3, 0, 3, 40.00, '{\"url\":\"https:\\/\\/vaquinhabr.com.br\\/aline\",\"domain\":\"vaquinhabr.com.br\",\"is_phishing\":false,\"confidence_score\":40,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":31,\"Domain Length\":17,\"Path Length\":6,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"Unknown\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-07-18 00:00:00\",\"Domain Status\":\"\",\"Domain Registrar\":\"Unknown\",\"Last Updated\":\"\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.registro.br\",\"Nameservers\":\"ns1034.hostgator.com.br, ns1035.hostgator.com.br\",\"Domain ID\":\"\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.617},\"whois_info\":{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Network Solutions, LLC\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"NETWORK-SOLUTIONS-HOSTING\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 40% (Low confidence in phishing detection)\\n\\n**AI Analysis**: safe\\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 01:28:38\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Network Solutions, LLC\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"NETWORK-SOLUTIONS-HOSTING\"}', 'Unknown', 'Network Solutions, LLC', 'Active', 0, NULL, NULL),
(4, 'banco-nacional.com', '2025-07-31 22:23:35', '2025-07-31 22:23:35', 1, 1, 0, 100.00, '{\"url\":\"https:\\/\\/banco-nacional.com\\/login\",\"domain\":\"banco-nacional.com\",\"is_phishing\":true,\"confidence_score\":100,\"phishing_result\":\"phishing\",\"features\":{\"URL Length\":32,\"Domain Length\":18,\"Path Length\":6,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":1,\"Domain Age\":\"305 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2025-09-30 11:20:05\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"REGTIME LTD.\",\"Last Updated\":\"2024-09-30 14:20:06\",\"Domain Owner\":\"Private person\",\"WHOIS Server\":\"whois.webnames.ru\",\"Nameservers\":\"LEX.NS.CLOUDFLARE.COM, TORI.NS.CLOUDFLARE.COM\",\"Domain ID\":\"2921344034_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.113},\"whois_info\":{\"Domain Age\":\"10 months 0 days\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"Regtime Ltd.\",\"Domain Expiry\":\"2025-09-30 11:20:05\",\"Last Updated\":\"2024-09-30 12:55:06\",\"Nameservers\":\"LEX.NS.CLOUDFLARE.COM, TORI.NS.CLOUDFLARE.COM\"},\"expert_analysis\":\"\\ud83d\\udea8 **HIGH RISK**: This URL has been identified as a potential phishing threat.\\n\\n**Confidence Level**: 100% (High confidence in phishing detection)\\n\\n**AI Analysis**: phishing\\n\\n**Risk Factors Detected**:\\n\\n\\u2022 Contains suspicious keywords\\n\\n**Recommendations**:\\n\\n\\u2022 Do not visit this URL\\n\\n\\u2022 Do not enter any personal information\\n\\n\\u2022 Report this URL to your IT security team\\n\\n\\u2022 Consider adding this domain to your blacklist\",\"scan_timestamp\":\"2025-08-01 01:23:35\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"10 months 0 days\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"Regtime Ltd.\",\"Domain Expiry\":\"2025-09-30 11:20:05\",\"Last Updated\":\"2024-09-30 12:55:06\",\"Nameservers\":\"LEX.NS.CLOUDFLARE.COM, TORI.NS.CLOUDFLARE.COM\"}', '10 months 0 days', 'Regtime Ltd.', 'ok https://icann.org/epp#ok', 0, NULL, NULL),
(5, 'gaalerabet.bet', '2025-07-31 22:23:58', '2025-07-31 22:23:58', 1, 1, 0, 100.00, '{\"url\":\"https:\\/\\/gaalerabet.bet\",\"domain\":\"gaalerabet.bet\",\"is_phishing\":true,\"confidence_score\":100,\"phishing_result\":\"phishing\",\"features\":{\"URL Length\":22,\"Domain Length\":14,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"2 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-07-29 13:00:28\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, addPeriod https:\\/\\/icann.org\\/epp#addPeriod, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"Spaceship, Inc.\",\"Last Updated\":\"2025-07-29 13:00:30\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.afilias.net\",\"Nameservers\":\"launch1.spaceship.net, launch2.spaceship.net\",\"Domain ID\":\"8f3e318c708f4988aff3332aad07f999-DONUTS\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":2.842},\"whois_info\":{\"Domain Age\":\"10 years 0 months\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"},\"expert_analysis\":\"\\ud83d\\udea8 **HIGH RISK**: This URL has been identified as a potential phishing threat.\\n\\n**Confidence Level**: 100% (High confidence in phishing detection)\\n\\n**AI Analysis**: phishing\\n\\n**Recommendations**:\\n\\n\\u2022 Do not visit this URL\\n\\n\\u2022 Do not enter any personal information\\n\\n\\u2022 Report this URL to your IT security team\\n\\n\\u2022 Consider adding this domain to your blacklist\",\"scan_timestamp\":\"2025-08-01 01:23:58\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"10 years 0 months\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}', '10 years 0 months', 'Unknown', 'ACTIVE', 0, NULL, NULL),
(6, 'allegro.oferta10924512.pl', '2025-07-31 22:28:43', '2025-07-31 22:28:43', 1, 1, 0, 100.00, '{\"url\":\"https:\\/\\/allegro.oferta10924512.pl\",\"domain\":\"allegro.oferta10924512.pl\",\"is_phishing\":true,\"confidence_score\":100,\"phishing_result\":\"phishing\",\"features\":{\"URL Length\":33,\"Domain Length\":25,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"Yes\",\"Contains Numbers in Domain\":\"Yes\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"Unknown\",\"Domain Registered\":\"No\",\"Domain Expiry\":\"Unknown\",\"Domain Status\":\"Status information unavailable\",\"Domain Registrar\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Domain Owner\":\"Private Registration\",\"WHOIS Server\":\"whois.dns.pl\",\"Nameservers\":\"Not Available\",\"Domain ID\":\"Unknown\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.894},\"whois_info\":{\"Domain Age\":\"35 years 0 months\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"},\"expert_analysis\":\"\\ud83d\\udea8 **HIGH RISK**: This URL has been identified as a potential phishing threat.\\n\\n**Confidence Level**: 100% (High confidence in phishing detection)\\n\\n**AI Analysis**: phishing\\n\\n**Recommendations**:\\n\\n\\u2022 Do not visit this URL\\n\\n\\u2022 Do not enter any personal information\\n\\n\\u2022 Report this URL to your IT security team\\n\\n\\u2022 Consider adding this domain to your blacklist\",\"scan_timestamp\":\"2025-08-01 01:28:43\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"35 years 0 months\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}', '35 years 0 months', 'Unknown', 'ACTIVE', 0, NULL, NULL),
(7, 'elopub.com', '2025-07-31 22:42:36', '2025-07-31 22:42:36', 1, 0, 1, 40.00, '{\"url\":\"http:\\/\\/elopub.com\\/winline\",\"domain\":\"elopub.com\",\"is_phishing\":false,\"confidence_score\":40,\"phishing_result\":\"\",\"features\":{\"URL Length\":25,\"Domain Length\":10,\"Path Length\":8,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"No\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"370 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-07-26 12:17:45\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"Beget LLC\",\"Last Updated\":\"2025-07-25 18:01:22\",\"Domain Owner\":\"Privacy Protect, LLC (PrivacyProtect.org)\",\"WHOIS Server\":\"whois.beget.com\",\"Nameservers\":\"NS1.BEGET.COM, NS1.BEGET.PRO, NS2.BEGET.COM, NS2.BEGET.PRO\",\"Domain ID\":\"2902578194_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.122},\"whois_info\":{\"Domain Age\":\"1 year 0 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"Beget LLC\",\"Domain Expiry\":\"2026-07-26 12:17:45\",\"Last Updated\":\"2025-07-25 18:01:21\",\"Nameservers\":\"NS1.BEGET.COM, NS1.BEGET.PRO, NS2.BEGET.COM, NS2.BEGET.PRO\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 40% (Low confidence in phishing detection)\\n\\n**AI Analysis**: \\n\\n**Risk Factors Detected**:\\n\\n\\u2022 Not using secure HTTPS connection\\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 01:42:36\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"1 year 0 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"Beget LLC\",\"Domain Expiry\":\"2026-07-26 12:17:45\",\"Last Updated\":\"2025-07-25 18:01:21\",\"Nameservers\":\"NS1.BEGET.COM, NS1.BEGET.PRO, NS2.BEGET.COM, NS2.BEGET.PRO\"}', '1 year 0 months', 'Beget LLC', 'clientTransferProhibited https://icann.org/epp#clientTransferProhibited', 0, NULL, NULL),
(8, 'cartaoame.com', '2025-07-31 22:43:13', '2025-07-31 22:43:13', 1, 0, 1, 40.00, '{\"url\":\"https:\\/\\/cartaoame.com\",\"domain\":\"cartaoame.com\",\"is_phishing\":false,\"confidence_score\":40,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":21,\"Domain Length\":13,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"42 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-06-19 03:26:11\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"Unknown\",\"Last Updated\":\"2025-06-19 03:26:31\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.tucows.com\",\"Nameservers\":\"ALINA.NS.CLOUDFLARE.COM, BENEDICT.NS.CLOUDFLARE.COM\",\"Domain ID\":\"2992977838_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":2.873},\"whois_info\":{\"Domain Age\":\"1 month 11 days\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"Tucows Domains Inc.\",\"Domain Expiry\":\"2026-06-19 03:26:11\",\"Last Updated\":\"2025-06-19 03:26:31\",\"Nameservers\":\"ALINA.NS.CLOUDFLARE.COM, BENEDICT.NS.CLOUDFLARE.COM\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 40% (Low confidence in phishing detection)\\n\\n**AI Analysis**: safe\\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 01:43:13\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"1 month 11 days\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"Tucows Domains Inc.\",\"Domain Expiry\":\"2026-06-19 03:26:11\",\"Last Updated\":\"2025-06-19 03:26:31\",\"Nameservers\":\"ALINA.NS.CLOUDFLARE.COM, BENEDICT.NS.CLOUDFLARE.COM\"}', '1 month 11 days', 'Tucows Domains Inc.', 'clientTransferProhibited https://icann.org/epp#clientTransferProhibited, clientUpdateProhibited http', 0, NULL, NULL),
(9, 't2m.io', '2025-07-31 22:43:47', '2025-07-31 22:43:47', 1, 0, 1, 40.00, '{\"url\":\"https:\\/\\/t2m.io\\/qhxu3paj\",\"domain\":\"t2m.io\",\"is_phishing\":false,\"confidence_score\":40,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":23,\"Domain Length\":6,\"Path Length\":9,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"Yes\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"3010 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-05-04 06:29:45\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NameCheap, Inc.\",\"Last Updated\":\"2025-04-15 16:22:43\",\"Domain Owner\":\"Privacy service provided by Withheld for Privacy ehf\",\"WHOIS Server\":\"whois.nic.io\",\"Nameservers\":\"terry.ns.cloudflare.com, grace.ns.cloudflare.com\",\"Domain ID\":\"c50cef46039b4e769ed2d38dcba6c9bf-DONUTS\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":2.585},\"whois_info\":{\"Domain Age\":\"8 years 2 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NameCheap, Inc.\",\"Domain Expiry\":\"2026-05-04 06:29:45\",\"Last Updated\":\"2025-04-15 16:22:43\",\"Nameservers\":\"terry.ns.cloudflare.com, grace.ns.cloudflare.com\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 40% (Low confidence in phishing detection)\\n\\n**AI Analysis**: safe\\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 01:43:47\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"8 years 2 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NameCheap, Inc.\",\"Domain Expiry\":\"2026-05-04 06:29:45\",\"Last Updated\":\"2025-04-15 16:22:43\",\"Nameservers\":\"terry.ns.cloudflare.com, grace.ns.cloudflare.com\"}', '8 years 2 months', 'NameCheap, Inc.', 'clientTransferProhibited https://icann.org/epp#clientTransferProhibited', 0, NULL, NULL),
(10, 'www.youtube.com', '2025-08-01 07:56:39', '2025-08-01 08:24:33', 3, 0, 3, 100.00, '{\"url\":\"https:\\/\\/www.youtube.com\",\"domain\":\"www.youtube.com\",\"is_phishing\":false,\"confidence_score\":100,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":23,\"Domain Length\":15,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"7472 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"MarkMonitor, Inc.\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Domain Owner\":\"Google LLC\",\"WHOIS Server\":\"whois.markmonitor.com\",\"Nameservers\":\"NS1.GOOGLE.COM, NS2.GOOGLE.COM, NS3.GOOGLE.COM, NS4.GOOGLE.COM\",\"Domain ID\":\"142504053_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.19},\"whois_info\":{\"Domain Age\":\"19 years 2 months\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"MarkMonitor Inc.\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Nameservers\":\"ns1.google.com, ns2.google.com, ns3.google.com, ns4.google.com\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL belongs to a known legitimate website and is safe to visit.\\n\\n**Confidence Level**: 100% (High confidence in safety - legitimate domain)\\n\\n**AI Analysis**: safe\\n\\n**Recommendations**:\\n\\n\\u2022 This URL belongs to a known legitimate website\\n\\n\\u2022 Safe to visit and use normally\\n\\n\\u2022 Always verify you\'re on the correct domain\",\"scan_timestamp\":\"2025-08-01 11:24:33\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"19 years 2 months\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"MarkMonitor Inc.\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Nameservers\":\"ns1.google.com, ns2.google.com, ns3.google.com, ns4.google.com\"}', '19 years 2 months', 'MarkMonitor Inc.', 'Active', 0, NULL, NULL),
(11, 'www.just.edu.so', '2025-08-01 08:01:02', '2025-08-01 08:25:13', 5, 0, 5, 100.00, '{\"url\":\"https:\\/\\/www.just.edu.so\",\"domain\":\"www.just.edu.so\",\"is_phishing\":false,\"confidence_score\":100,\"phishing_result\":\"\",\"features\":{\"URL Length\":23,\"Domain Length\":15,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":3,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"Yes\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"5388 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-12-31 00:00:00\",\"Domain Status\":\"inactive https:\\/\\/icann.org\\/epp#inactive, clientRenewProhibited https:\\/\\/icann.org\\/epp#clientRenewProhibited, serverRenewProhibited https:\\/\\/icann.org\\/epp#serverRenewProhibited\",\"Domain Registrar\":\"soNIC Reserved\",\"Last Updated\":\"2024-03-29 06:43:08\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.nic.so\",\"Nameservers\":\"Not Available\",\"Domain ID\":\"18-sonic\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.006},\"whois_info\":{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Hetzner Online GmbH\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"HETZNER-AS\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 100% (Low confidence in phishing detection)\\n\\n**AI Analysis**: \\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 11:25:13\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Hetzner Online GmbH\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"HETZNER-AS\"}', 'Unknown', 'Hetzner Online GmbH', 'Active', 0, NULL, NULL),
(12, 'jazeerauniversity.edu.so', '2025-08-01 08:01:34', '2025-08-01 08:01:34', 1, 0, 1, 100.00, '{\"url\":\"https:\\/\\/jazeerauniversity.edu.so\",\"domain\":\"jazeerauniversity.edu.so\",\"is_phishing\":false,\"confidence_score\":100,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":32,\"Domain Length\":24,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"Yes\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"5388 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-12-31 00:00:00\",\"Domain Status\":\"inactive https:\\/\\/icann.org\\/epp#inactive, clientRenewProhibited https:\\/\\/icann.org\\/epp#clientRenewProhibited, serverRenewProhibited https:\\/\\/icann.org\\/epp#serverRenewProhibited\",\"Domain Registrar\":\"soNIC Reserved\",\"Last Updated\":\"2024-03-29 06:43:08\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.nic.so\",\"Nameservers\":\"Not Available\",\"Domain ID\":\"18-sonic\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.752},\"whois_info\":{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (located in United States)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL appears to be legitimate and safe to visit.\\n\\n**Confidence Level**: 100% (Low confidence in phishing detection)\\n\\n**AI Analysis**: safe\\n\\n**Risk Factors Detected**:\\n\\n\\u2022 Contains random strings (common in phishing)\\n\\n**Recommendations**:\\n\\n\\u2022 This URL appears safe to visit\\n\\n\\u2022 Always verify the domain name carefully\\n\\n\\u2022 Check for HTTPS security certificate\",\"scan_timestamp\":\"2025-08-01 11:01:34\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (located in United States)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}', 'Unknown', 'Unknown', 'Active (located in United States)', 0, NULL, NULL),
(13, 'youtube.com', '2025-08-01 08:05:54', '2025-08-01 08:05:54', 1, 0, 1, 100.00, '{\"url\":\"https:\\/\\/youtube.com\",\"domain\":\"youtube.com\",\"is_phishing\":false,\"confidence_score\":100,\"phishing_result\":\"safe\",\"features\":{\"URL Length\":19,\"Domain Length\":11,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"7472 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"MarkMonitor, Inc.\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Domain Owner\":\"Google LLC\",\"WHOIS Server\":\"whois.markmonitor.com\",\"Nameservers\":\"NS1.GOOGLE.COM, NS2.GOOGLE.COM, NS3.GOOGLE.COM, NS4.GOOGLE.COM\",\"Domain ID\":\"142504053_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.096},\"whois_info\":{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (located in United States)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"},\"expert_analysis\":\"\\u2705 **SAFE**: This URL belongs to a known legitimate website and is safe to visit.\\n\\n**Confidence Level**: 100% (High confidence in safety - legitimate domain)\\n\\n**AI Analysis**: safe\\n\\n**Recommendations**:\\n\\n\\u2022 This URL belongs to a known legitimate website\\n\\n\\u2022 Safe to visit and use normally\\n\\n\\u2022 Always verify you\'re on the correct domain\",\"scan_timestamp\":\"2025-08-01 11:05:54\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (located in United States)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}', 'Unknown', 'Unknown', 'Active (located in United States)', 0, NULL, NULL),
(14, 'blaze-award.net', '2025-08-01 08:26:24', '2025-08-01 08:26:24', 1, 1, 0, 100.00, '{\"url\":\"https:\\/\\/blaze-award.net\",\"domain\":\"blaze-award.net\",\"is_phishing\":true,\"confidence_score\":100,\"phishing_result\":\"phishing\",\"features\":{\"URL Length\":23,\"Domain Length\":15,\"Path Length\":0,\"Query Length\":0,\"Dots in Domain\":1,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"1 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-07-30 18:53:50\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NICENIC INTERNATIONAL GROUP CO., LIMITED\",\"Last Updated\":\"2025-07-30 18:55:17\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.nicenic.net\",\"Nameservers\":\"DEVIN.NS.CLOUDFLARE.COM, ZOE.NS.CLOUDFLARE.COM\",\"Domain ID\":\"3005623161_DOMAIN_NET-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.457},\"whois_info\":{\"Domain Age\":\"1 day\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NICENIC INTERNATIONAL GROUP CO., LIMITED\",\"Domain Expiry\":\"2026-07-30 18:53:50\",\"Last Updated\":\"2025-07-30 18:55:17\",\"Nameservers\":\"DEVIN.NS.CLOUDFLARE.COM, ZOE.NS.CLOUDFLARE.COM\"},\"expert_analysis\":\"\\ud83d\\udea8 **HIGH RISK**: This URL has been identified as a potential phishing threat.\\n\\n**Confidence Level**: 100% (High confidence in phishing detection)\\n\\n**AI Analysis**: phishing\\n\\n**Recommendations**:\\n\\n\\u2022 Do not visit this URL\\n\\n\\u2022 Do not enter any personal information\\n\\n\\u2022 Report this URL to your IT security team\\n\\n\\u2022 Consider adding this domain to your blacklist\",\"scan_timestamp\":\"2025-08-01 11:26:24\",\"status\":\"completed\",\"user_id\":9,\"is_admin_scan\":true}', '{\"Domain Age\":\"1 day\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NICENIC INTERNATIONAL GROUP CO., LIMITED\",\"Domain Expiry\":\"2026-07-30 18:53:50\",\"Last Updated\":\"2025-07-30 18:55:17\",\"Nameservers\":\"DEVIN.NS.CLOUDFLARE.COM, ZOE.NS.CLOUDFLARE.COM\"}', '1 day', 'NICENIC INTERNATIONAL GROUP CO., LIMITED', 'clientDeleteProhibited https://icann.org/epp#clientDeleteProhibited, clientTransferProhibited https:', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `url_scans`
--

CREATE TABLE `url_scans` (
  `id` int(11) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_phishing` tinyint(1) NOT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `scan_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `risk_level` varchar(20) DEFAULT 'UNKNOWN',
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `expert_analysis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expert_analysis`)),
  `whois_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`whois_info`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `url_scans`
--

INSERT INTO `url_scans` (`id`, `url`, `user_id`, `is_phishing`, `confidence_score`, `scan_date`, `risk_level`, `features`, `expert_analysis`, `whois_info`) VALUES
(84, 'https://rtcnv.nl/', 9, 0, NULL, '2025-07-26 04:02:14', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"info\":\"Unfortunately, I am unable to provide WHOIS and domain information for specific URLs. You can obtain this information by visiting a WHOIS lookup service or contacting the domain registrar directly.\"}'),
(85, 'https://rtcnv.nl/', 9, 0, NULL, '2025-07-26 04:02:49', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"info\":\"The domain https:\\/\\/rtcnv.nl\\/ is registered in the Netherlands. The WHOIS information indicates that the domain was registered on February 4, 2004. The domain appears to be associated with a company or organization. Additional detailed WHOIS information may require a paid service.\"}'),
(86, 'https://ttttttttrtcnv.nl/', 9, 1, NULL, '2025-07-26 04:06:14', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"info\":null}'),
(87, 'https://www.youtube.com/', 9, 0, NULL, '2025-07-27 03:35:05', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(88, 'https://rtcnv.nl/', 9, 0, NULL, '2025-07-27 03:35:55', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"Domain Age\":\"39 years 3 months\",\"Domain Status\":\"ACTIVE\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(89, 'https://corporatefinanceinstitute.com', 9, 0, NULL, '2025-07-27 03:38:41', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"<\\/div><div class=\\\"df-value\\\">NameCheap, Inc.<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">IANA ID:<\\/div><div class=\\\"df-value\\\">1068<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Abuse Email:<\\/div><div class=\\\"df-value\\\">abuse@namecheap.com<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Abuse Phone:<\\/div><div class=\\\"df-value\\\">+1.6613102107<\\/div><\\/div><\\/div><div class=\\\"df-block\\\"><div class=\\\"df-heading\\\"><span class=\\\"df-ico-regcon\\\"><\\/span>Registrant Contact<\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Organization:<\\/div><div class=\\\"df-value\\\">Privacy service provided by Withheld for Privacy ehf<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Street:<\\/div><div class=\\\"df-value\\\">Kalkofnsvegur 2<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">City:<\\/div><div class=\\\"df-value\\\">Reykjavik<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">State:<\\/div><div class=\\\"df-value\\\">Capital Region<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Postal Code:<\\/div><div class=\\\"df-value\\\">101<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Country:<\\/div><div class=\\\"df-value\\\">IS<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Phone:<\\/div><div class=\\\"df-value\\\">+354.4212434<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Email:<\\/div><div class=\\\"df-value\\\"><img src=\\\"\\/eimg\\/5\\/a0\\/5a0e844a1fdf26b93c9a7cb45905c7e99f4d12b0.png\\\" class=\\\"email\\\" alt=\\\"email\\\" loading=\\\"lazy\\\">@withheldforprivacy.com<\\/div><\\/div><\\/div><div class=\\\"df-block\\\"><div class=\\\"df-heading\\\"><span class=\\\"df-ico-admcon\\\"><\\/span>Administrative Contact<\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Organization:<\\/div><div class=\\\"df-value\\\">Privacy service provided by Withheld for Privacy ehf<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Street:<\\/div><div class=\\\"df-value\\\">Kalkofnsvegur 2<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">City:<\\/div><div class=\\\"df-value\\\">Reykjavik<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">State:<\\/div><div class=\\\"df-value\\\">Capital Region<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Postal Code:<\\/div><div class=\\\"df-value\\\">101<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Country:<\\/div><div class=\\\"df-value\\\">IS<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Phone:<\\/div><div class=\\\"df-value\\\">+354.4212434<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Email:<\\/div><div class=\\\"df-value\\\"><img src=\\\"\\/eimg\\/5\\/a0\\/5a0e844a1fdf26b93c9a7cb45905c7e99f4d12b0.png\\\" class=\\\"email\\\" alt=\\\"email\\\" loading=\\\"lazy\\\">@withheldforprivacy.com<\\/div><\\/div><\\/div><div class=\\\"df-block\\\"><div class=\\\"df-heading\\\"><span class=\\\"df-ico-tekcon\\\"><\\/span>Technical Contact<\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Organization:<\\/div><div class=\\\"df-value\\\">Privacy service provided by Withheld for Privacy ehf<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Street:<\\/div><div class=\\\"df-value\\\">Kalkofnsvegur 2<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">City:<\\/div><div class=\\\"df-value\\\">Reykjavik<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">State:<\\/div><div class=\\\"df-value\\\">Capital Region<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Postal Code:<\\/div><div class=\\\"df-value\\\">101<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Country:<\\/div><div class=\\\"df-value\\\">IS<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Phone:<\\/div><div class=\\\"df-value\\\">+354.4212434<\\/div><\\/div><div class=\\\"df-row\\\"><div class=\\\"df-label\\\">Email:<\\/div><div class=\\\"df-value\\\"><img src=\\\"\\/eimg\\/5\\/a0\\/5a0e844a1fdf26b93c9a7cb45905c7e99f4d12b0.png\\\" class=\\\"email\\\" alt=\\\"email\\\" loading=\\\"lazy\\\">@withheldforprivacy.com<\\/div><\\/div><\\/div><\\/div>\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(90, 'https://corporatefinanceinstitute.com/', 9, 0, NULL, '2025-07-27 03:42:25', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Cloudflare, Inc.\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"CLOUDFLARENET\"}'),
(91, 'https://www.youtube.com', 9, 0, NULL, '2025-07-27 03:45:55', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (has CNAME)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(92, 'https://corporatefinanceinstitute.com', 9, 0, NULL, '2025-07-27 04:19:17', 'LOW', '[]', '{\"explanations\":[\"safe\"]}', '{\"Domain Age\":\"9 years 8 months\",\"Domain Status\":\"clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited\",\"Domain Registrar\":\"NameCheap, Inc.\",\"Domain Expiry\":\"2032-10-29 04:03:07\",\"Last Updated\":\"2023-07-11 21:16:25\",\"Nameservers\":\"CHUCK.NS.CLOUDFLARE.COM, DEB.NS.CLOUDFLARE.COM\"}'),
(93, 'http://allegrolokalnie.pl-oferta8702097.sbs', 9, 1, NULL, '2025-07-27 04:41:51', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(94, 'https://allegrolokalnie.pl-oferta8702097.sbs', 9, 1, NULL, '2025-07-27 04:42:51', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(95, 'http://allegro.pl-kategorie78126373167215.shop', 9, 1, NULL, '2025-07-27 04:44:24', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(96, 'https://allegro.pl-kategorie78126373167215.shop', 9, 1, NULL, '2025-07-27 04:46:36', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(97, 'https://sinarmascoid.com', 9, 1, NULL, '2025-07-27 04:47:09', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"8 months 1 day\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"Squarespace Domains II LLC\",\"Domain Expiry\":\"2025-11-26 04:39:32\",\"Last Updated\":\"2025-05-27 14:58:51\",\"Nameservers\":\"GUSS.NS.CLOUDFLARE.COM, MINA.NS.CLOUDFLARE.COM\"}'),
(98, 'https://allegrolokalnie.pi-59282845.rest', 9, 1, NULL, '2025-07-27 04:47:47', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(99, 'https://allegro.pi-58217582.click', 9, 1, NULL, '2025-07-27 04:48:07', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(100, 'https://allegro.pl-92745835.cfd', 9, 1, NULL, '2025-07-27 04:48:28', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(101, 'https://jazeerauniversity.edu.so', 9, 0, 100.00, '2025-07-27 04:49:04', 'LOW', '[]', '{\"explanations\":[\"Somalia university domain (.edu.so) is always safe.\"]}', '{\"Domain Age\":\"12 years 5 months\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, serverDeleteProhibited https:\\/\\/icann.org\\/epp#serverDeleteProhibited\",\"Domain Registrar\":\"SoNIC DotEDU Registry\",\"Domain Expiry\":\"2026-12-31 00:00:00\",\"Last Updated\":\"2025-02-20 12:39:52\",\"Nameservers\":\"ns8305.hostgator.com, ns8306.hostgator.com\"}'),
(102, 'https://just.edu.so', 9, 0, 100.00, '2025-07-27 04:50:09', 'LOW', '[]', '{\"explanations\":[\"Somalia university domain (.edu.so) is always safe.\"]}', '{\"Domain Age\":\"13 years 2 months\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, serverDeleteProhibited https:\\/\\/icann.org\\/epp#serverDeleteProhibited\",\"Domain Registrar\":\"SoNIC DotEDU Registry\",\"Domain Expiry\":\"2026-12-31 00:00:00\",\"Last Updated\":\"2025-02-20 12:39:52\",\"Nameservers\":\"ns1.stackdns.com, ns2.stackdns.com, ns3.stackdns.com, ns4.stackdns.com\"}'),
(103, 'https://jazeerauniversity.edu.so', 9, 0, 100.00, '2025-07-27 04:50:26', 'LOW', '[]', '{\"explanations\":[\"Somalia university domain (.edu.so) is always safe.\"]}', '{\"Domain Age\":\"12 years 5 months\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, serverDeleteProhibited https:\\/\\/icann.org\\/epp#serverDeleteProhibited\",\"Domain Registrar\":\"SoNIC DotEDU Registry\",\"Domain Expiry\":\"2026-12-31 00:00:00\",\"Last Updated\":\"2025-02-20 12:39:52\",\"Nameservers\":\"ns8305.hostgator.com, ns8306.hostgator.com\"}'),
(104, 'http://allegro.pl-92745835.cfd', NULL, 1, NULL, '2025-07-27 04:52:39', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(105, 'http://sinarmascoid.com', NULL, 1, NULL, '2025-07-27 04:53:21', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"8 months 1 day\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"Squarespace Domains II LLC\",\"Domain Expiry\":\"2025-11-26 04:39:32\",\"Last Updated\":\"2025-05-27 14:58:51\",\"Nameservers\":\"GUSS.NS.CLOUDFLARE.COM, MINA.NS.CLOUDFLARE.COM\"}'),
(106, 'http://sinarmascoid.com', NULL, 1, NULL, '2025-07-27 05:01:21', 'HIGH', '[]', '{\"explanations\":[\"phishing\"]}', '{\"Domain Age\":\"8 months 1 day\",\"Domain Status\":\"ok https:\\/\\/icann.org\\/epp#ok\",\"Domain Registrar\":\"Squarespace Domains II LLC\",\"Domain Expiry\":\"2025-11-26 04:39:32\",\"Last Updated\":\"2025-05-27 14:58:51\",\"Nameservers\":\"GUSS.NS.CLOUDFLARE.COM, MINA.NS.CLOUDFLARE.COM\"}'),
(107, 'https://www.youtube.com/', 9, 0, 15.00, '2025-07-29 03:34:05', 'LOW', '{\"URL Length\":24,\"Domain Length\":15,\"Path Length\":1,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"7468 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"MarkMonitor, Inc.\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Domain Owner\":\"Google LLC\",\"WHOIS Server\":\"whois.markmonitor.com\",\"Nameservers\":\"NS1.GOOGLE.COM, NS2.GOOGLE.COM, NS3.GOOGLE.COM, NS4.GOOGLE.COM\",\"Domain ID\":\"142504053_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.19}', '{\"explanations\":[\"R1 AI Model: safe\"],\"safe_occurrences\":1,\"phishing_occurrences\":0,\"blacklist_status\":\"Not Blacklisted\",\"server_ip\":\"172.24.48.1\",\"asn\":\"Unknown\",\"geo\":{\"country\":\"Unknown\"},\"history\":[]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (has CNAME)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"Unknown\"}'),
(108, 'https://www.youtube.com/', 9, 0, 15.00, '2025-07-29 03:43:54', 'LOW', '{\"URL Length\":24,\"Domain Length\":15,\"Path Length\":1,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"7468 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"MarkMonitor, Inc.\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Domain Owner\":\"Google LLC\",\"WHOIS Server\":\"whois.markmonitor.com\",\"Nameservers\":\"NS1.GOOGLE.COM, NS2.GOOGLE.COM, NS3.GOOGLE.COM, NS4.GOOGLE.COM\",\"Domain ID\":\"142504053_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.19}', '{\"explanations\":[\"R1 AI Model: safe\"],\"safe_occurrences\":1,\"phishing_occurrences\":0,\"blacklist_status\":\"Not Blacklisted\",\"server_ip\":\"172.24.48.1\",\"asn\":\"Unknown\",\"geo\":{\"country\":\"Unknown\"},\"history\":[]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (SSL secured)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"2025-07-07 10:34:03\",\"Nameservers\":\"Unknown\"}'),
(109, 'https://www.just.edu.so/', 9, 0, 15.00, '2025-07-29 04:15:26', 'LOW', '{\"URL Length\":24,\"Domain Length\":15,\"Path Length\":1,\"Query Length\":0,\"Dots in Domain\":3,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"Yes\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"5385 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-12-31 00:00:00\",\"Domain Status\":\"inactive https:\\/\\/icann.org\\/epp#inactive, clientRenewProhibited https:\\/\\/icann.org\\/epp#clientRenewProhibited, serverRenewProhibited https:\\/\\/icann.org\\/epp#serverRenewProhibited\",\"Domain Registrar\":\"soNIC Reserved\",\"Last Updated\":\"2024-03-29 06:43:08\",\"Domain Owner\":\"Disabled due to GDPR\",\"WHOIS Server\":\"whois.nic.so\",\"Nameservers\":\"Not Available\",\"Domain ID\":\"18-sonic\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.006}', '{\"explanations\":[\"R1 AI Model: Somalia university domain (.edu.so) is always safe.\"],\"safe_occurrences\":1,\"phishing_occurrences\":0,\"blacklist_status\":\"Not Blacklisted\",\"server_ip\":\"172.24.48.1\",\"asn\":\"Unknown\",\"geo\":{\"country\":\"Unknown\"},\"history\":[]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active (SSL secured)\",\"Domain Registrar\":\"Unknown\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"2025-07-26 14:20:10\",\"Nameservers\":\"Unknown\"}'),
(110, 'https://www.youtube.com/', 9, 0, 15.00, '2025-07-29 05:21:08', 'LOW', '{\"URL Length\":24,\"Domain Length\":15,\"Path Length\":1,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"7469 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"MarkMonitor, Inc.\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Domain Owner\":\"Google LLC\",\"WHOIS Server\":\"whois.markmonitor.com\",\"Nameservers\":\"NS1.GOOGLE.COM, NS2.GOOGLE.COM, NS3.GOOGLE.COM, NS4.GOOGLE.COM\",\"Domain ID\":\"142504053_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.19}', '{\"explanations\":[\"R1 AI Model: safe\"],\"safe_occurrences\":1,\"phishing_occurrences\":0,\"blacklist_status\":\"Not Blacklisted\",\"server_ip\":\"172.24.48.1\",\"asn\":\"Unknown\",\"geo\":{\"country\":\"Unknown\"},\"history\":[]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Google LLC\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"GOOGLE\"}'),
(111, 'https://www.youtube.com/', 9, 0, 15.00, '2025-07-29 05:45:02', 'LOW', '{\"URL Length\":24,\"Domain Length\":15,\"Path Length\":1,\"Query Length\":0,\"Dots in Domain\":2,\"Contains IP Address\":\"No\",\"Contains @ Symbol\":\"No\",\"Uses HTTPS\":\"Yes\",\"Has Multiple Subdomains\":\"No\",\"Contains Hexadecimal\":\"No\",\"Contains Numbers in Domain\":\"No\",\"Contains Special Chars\":\"No\",\"Contains Random String\":\"No\",\"Suspicious TLD\":\"No\",\"Contains Brand Name\":\"No\",\"Brand Name Count\":0,\"Suspicious Words\":0,\"Domain Age\":\"7469 days\",\"Domain Registered\":\"Yes\",\"Domain Expiry\":\"2026-02-15 05:13:12\",\"Domain Status\":\"clientDeleteProhibited https:\\/\\/icann.org\\/epp#clientDeleteProhibited, clientTransferProhibited https:\\/\\/icann.org\\/epp#clientTransferProhibited, clientUpdateProhibited https:\\/\\/icann.org\\/epp#clientUpdateProhibited\",\"Domain Registrar\":\"MarkMonitor, Inc.\",\"Last Updated\":\"2025-01-14 10:06:34\",\"Domain Owner\":\"Google LLC\",\"WHOIS Server\":\"whois.markmonitor.com\",\"Nameservers\":\"NS1.GOOGLE.COM, NS2.GOOGLE.COM, NS3.GOOGLE.COM, NS4.GOOGLE.COM\",\"Domain ID\":\"142504053_DOMAIN_COM-VRSN\",\"Status Description\":\"Successfully processed\",\"Entropy Score\":3.19}', '{\"explanations\":[\"R1 AI Model: safe\"],\"safe_occurrences\":1,\"phishing_occurrences\":0,\"blacklist_status\":\"Not Blacklisted\",\"server_ip\":\"172.24.48.1\",\"asn\":\"Unknown\",\"geo\":{\"country\":\"Unknown\"},\"history\":[]}', '{\"Domain Age\":\"Unknown\",\"Domain Status\":\"Active\",\"Domain Registrar\":\"Google LLC\",\"Domain Expiry\":\"Unknown\",\"Last Updated\":\"Unknown\",\"Nameservers\":\"GOOGLE\"}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `last_login`, `created_at`) VALUES
(9, 'hamza1', 'iamcaano2@gmail.com', '$2y$10$r9K9DhaW8H7E/uALJ5KyPuEVHat6SU9SbrqMVyEZU4SgwMD6U7RGK', 'admin', 'active', '2025-08-01 01:35:57', '2025-07-24 19:34:51'),
(12, 'admin', 'admin@example.com', '$2y$10$VCKS/PuBpQKcdjelJAzn0eos8rj7OmuWzGqzECfBDJh6GBIyK2QoS', 'admin', 'active', NULL, '2025-07-31 22:57:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `idx_api_keys_user` (`user_id`);

--
-- Indexes for table `domain_blacklist`
--
ALTER TABLE `domain_blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domain_reports`
--
ALTER TABLE `domain_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `idx_domain` (`domain`),
  ADD KEY `idx_domain_reports_date` (`report_date`);

--
-- Indexes for table `scanned_domains`
--
ALTER TABLE `scanned_domains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_domain` (`domain`),
  ADD KEY `idx_last_scan_date` (`last_scan_date`),
  ADD KEY `idx_is_blacklisted` (`is_blacklisted`);

--
-- Indexes for table `url_scans`
--
ALTER TABLE `url_scans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_url_scans_user` (`user_id`),
  ADD KEY `idx_url_scans_date` (`scan_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `domain_blacklist`
--
ALTER TABLE `domain_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `domain_reports`
--
ALTER TABLE `domain_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205651;

--
-- AUTO_INCREMENT for table `scanned_domains`
--
ALTER TABLE `scanned_domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `url_scans`
--
ALTER TABLE `url_scans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD CONSTRAINT `api_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `domain_reports`
--
ALTER TABLE `domain_reports`
  ADD CONSTRAINT `domain_reports_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `url_scans`
--
ALTER TABLE `url_scans`
  ADD CONSTRAINT `url_scans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

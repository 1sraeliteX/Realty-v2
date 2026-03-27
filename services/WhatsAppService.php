<?php

/**
 * WhatsApp API Service
 * Integrates with WhatsApp Business API for sending messages
 */

class WhatsAppService {
    private $accessToken;
    private $phoneNumberId;
    private $apiVersion;
    private $baseUrl;
    
    public function __construct() {
        $this->accessToken = $_ENV['WHATSAPP_ACCESS_TOKEN'] ?? '';
        $this->phoneNumberId = $_ENV['WHATSAPP_PHONE_NUMBER_ID'] ?? '';
        $this->apiVersion = $_ENV['WHATSAPP_API_VERSION'] ?? 'v18.0';
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}";
        
        if (empty($this->accessToken) || empty($this->phoneNumberId)) {
            error_log("WhatsApp API credentials not configured");
        }
    }
    
    /**
     * Send text message via WhatsApp
     */
    public function sendTextMessage($recipientPhone, $message) {
        if (empty($this->accessToken) || empty($this->phoneNumberId)) {
            return ['success' => false, 'message' => 'WhatsApp API not configured'];
        }
        
        // Format phone number (remove +, spaces, dashes)
        $recipientPhone = preg_replace('/[^0-9]/', '', $recipientPhone);
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientPhone,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];
        
        return $this->makeApiRequest("/{$this->phoneNumberId}/messages", $payload);
    }
    
    /**
     * Send template message via WhatsApp
     */
    public function sendTemplateMessage($recipientPhone, $templateName, $language = 'en_US', $components = []) {
        if (empty($this->accessToken) || empty($this->phoneNumberId)) {
            return ['success' => false, 'message' => 'WhatsApp API not configured'];
        }
        
        // Format phone number
        $recipientPhone = preg_replace('/[^0-9]/', '', $recipientPhone);
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientPhone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language
                ]
            ]
        ];
        
        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }
        
        return $this->makeApiRequest("/{$this->phoneNumberId}/messages", $payload);
    }
    
    /**
     * Send message with media (image, document, etc.)
     */
    public function sendMediaMessage($recipientPhone, $mediaType, $mediaUrl, $caption = '') {
        if (empty($this->accessToken) || empty($this->phoneNumberId)) {
            return ['success' => false, 'message' => 'WhatsApp API not configured'];
        }
        
        // Format phone number
        $recipientPhone = preg_replace('/[^0-9]/', '', $recipientPhone);
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientPhone,
            'type' => $mediaType,
            $mediaType => [
                'link' => $mediaUrl
            ]
        ];
        
        if (!empty($caption)) {
            $payload[$mediaType]['caption'] = $caption;
        }
        
        return $this->makeApiRequest("/{$this->phoneNumberId}/messages", $payload);
    }
    
    /**
     * Make API request to WhatsApp
     */
    private function makeApiRequest($endpoint, $payload) {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("WhatsApp API cURL error: " . $error);
            return ['success' => false, 'message' => 'API request failed: ' . $error];
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode !== 200) {
            error_log("WhatsApp API error: HTTP {$httpCode} - " . $response);
            return [
                'success' => false, 
                'message' => $responseData['error']['message'] ?? 'API request failed',
                'error_code' => $httpCode
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $responseData
        ];
    }
    
    /**
     * Verify phone number format
     */
    public function validatePhoneNumber($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid international number (8-15 digits)
        if (strlen($phone) < 8 || strlen($phone) > 15) {
            return false;
        }
        
        return $phone;
    }
    
    /**
     * Get message templates from WhatsApp
     */
    public function getTemplates() {
        if (empty($this->accessToken)) {
            return ['success' => false, 'message' => 'WhatsApp API not configured'];
        }
        
        $url = $this->baseUrl . "/{$this->phoneNumberId}/message_templates";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return ['success' => false, 'message' => 'Failed to fetch templates'];
        }
        
        $responseData = json_decode($response, true);
        return [
            'success' => true,
            'templates' => $responseData['data'] ?? []
        ];
    }
    
    /**
     * Check if WhatsApp API is configured
     */
    public function isConfigured() {
        return !empty($this->accessToken) && !empty($this->phoneNumberId);
    }
}
?>

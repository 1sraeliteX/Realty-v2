<?php

/**
 * Message Template Service
 * Manages message templates for different communication channels
 */

class MessageTemplateService {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all templates for an admin
     */
    public function getTemplates($adminId, $type = null) {
        $sql = "SELECT * FROM message_templates WHERE admin_id = ? AND deleted_at IS NULL";
        $params = [$adminId];
        
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get template by ID
     */
    public function getTemplate($id, $adminId) {
        $templates = $this->db->fetchAll(
            "SELECT * FROM message_templates WHERE id = ? AND admin_id = ? AND deleted_at IS NULL",
            [$id, $adminId]
        );
        
        return $templates[0] ?? null;
    }
    
    /**
     * Create new template
     */
    public function createTemplate($adminId, $data) {
        $templateData = [
            'admin_id' => $adminId,
            'name' => $data['name'],
            'type' => $data['type'],
            'subject' => $data['subject'] ?? '',
            'message' => $data['message'],
            'variables' => json_encode($data['variables'] ?? []),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('message_templates', $templateData);
    }
    
    /**
     * Update template
     */
    public function updateTemplate($id, $adminId, $data) {
        $updateData = [
            'name' => $data['name'],
            'subject' => $data['subject'] ?? '',
            'message' => $data['message'],
            'variables' => json_encode($data['variables'] ?? []),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->update('message_templates', $updateData, 'id = ? AND admin_id = ?', [$id, $adminId]);
    }
    
    /**
     * Delete template (soft delete)
     */
    public function deleteTemplate($id, $adminId) {
        return $this->db->update('message_templates', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ? AND admin_id = ?', [$id, $adminId]);
    }
    
    /**
     * Process template with variables
     */
    public function processTemplate($template, $variables = []) {
        $message = $template['message'];
        $subject = $template['subject'] ?? '';
        
        // Replace variables in message and subject
        $templateVariables = json_decode($template['variables'] ?? '[]', true);
        
        foreach ($templateVariables as $var) {
            $placeholder = "{{" . $var . "}}";
            $value = $variables[$var] ?? '[' . $var . ']';
            
            $message = str_replace($placeholder, $value, $message);
            $subject = str_replace($placeholder, $value, $subject);
        }
        
        return [
            'subject' => $subject,
            'message' => $message
        ];
    }
    
    /**
     * Get default templates
     */
    public function getDefaultTemplates() {
        return [
            // Email Templates
            [
                'name' => 'Rent Reminder',
                'type' => 'email',
                'subject' => 'Rent Payment Reminder - {{property_name}}',
                'message' => 'Dear {{tenant_name}},\n\nThis is a friendly reminder that your rent payment of {{rent_amount}} for {{property_name}} is due on {{due_date}}.\n\nPlease ensure payment is made on time to avoid late fees.\n\nThank you,\n{{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'rent_amount', 'due_date', 'property_manager']
            ],
            [
                'name' => 'Maintenance Update',
                'type' => 'email',
                'subject' => 'Maintenance Update - {{property_name}}',
                'message' => 'Dear {{tenant_name}},\n\nWe wanted to update you on the maintenance request for {{property_name}}.\n\nStatus: {{status}}\nDetails: {{details}}\n\nIf you have any questions, please contact us.\n\nThank you,\n{{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'status', 'details', 'property_manager']
            ],
            [
                'name' => 'Welcome Message',
                'type' => 'email',
                'subject' => 'Welcome to {{property_name}}!',
                'message' => 'Dear {{tenant_name}},\n\nWelcome to your new home at {{property_name}}! We\'re excited to have you as our resident.\n\nProperty Manager: {{property_manager}}\nContact: {{contact_number}}\n\nFeel free to reach out if you need anything!\n\nBest regards,\n{{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'property_manager', 'contact_number']
            ],
            [
                'name' => 'Lease Renewal',
                'type' => 'email',
                'subject' => 'Lease Renewal - {{property_name}}',
                'message' => 'Dear {{tenant_name}},\n\nYour lease for {{property_name}} is set to expire on {{expiry_date}}. We would love to have you stay with us!\n\nNew lease terms:\n- Duration: {{new_duration}}\n- Rent: {{new_rent}}\n- Start date: {{new_start_date}}\n\nPlease let us know if you\'d like to renew.\n\nBest regards,\n{{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'expiry_date', 'new_duration', 'new_rent', 'new_start_date', 'property_manager']
            ],
            [
                'name' => 'Payment Confirmation',
                'type' => 'email',
                'subject' => 'Payment Confirmation - {{property_name}}',
                'message' => 'Dear {{tenant_name}},\n\nWe\'ve received your rent payment of {{amount}} for {{property_name}}. Thank you!\n\nPayment details:\n- Amount: {{amount}}\n- Property: {{property_name}}\n- Date: {{payment_date}}\n\nIf you have any questions, please contact us.\n\nThank you,\n{{property_manager}}',
                'variables' => ['tenant_name', 'amount', 'property_name', 'payment_date', 'property_manager']
            ],
            
            // SMS Templates
            [
                'name' => 'Rent Reminder',
                'type' => 'sms',
                'subject' => '',
                'message' => 'Hi {{tenant_name}}! Rent of {{rent_amount}} for {{property_name}} is due on {{due_date}}. Please pay on time to avoid fees. Thanks! - {{property_manager}}',
                'variables' => ['tenant_name', 'rent_amount', 'property_name', 'due_date', 'property_manager']
            ],
            [
                'name' => 'Welcome Message',
                'type' => 'sms',
                'subject' => '',
                'message' => 'Welcome {{tenant_name}} to {{property_name}}! We\'re excited to have you. Contact: {{contact_number}}. Thanks! - {{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'property_manager', 'contact_number']
            ],
            [
                'name' => 'Payment Confirmation',
                'type' => 'sms',
                'subject' => '',
                'message' => 'Hi {{tenant_name}}, we\'ve received your rent payment of {{amount}} for {{property_name}}. Thank you! - {{property_manager}}',
                'variables' => ['tenant_name', 'amount', 'property_name', 'property_manager']
            ],
            [
                'name' => 'Maintenance Notification',
                'type' => 'sms',
                'subject' => '',
                'message' => 'Hi {{tenant_name}}, update on your maintenance request at {{property_name}}: {{status}} - {{details}}. Contact us if needed! - {{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'status', 'details', 'property_manager']
            ],
            
            // WhatsApp Templates
            [
                'name' => 'Welcome Message',
                'type' => 'whatsapp',
                'subject' => '',
                'message' => 'Welcome {{tenant_name}}! 🏠\n\nThank you for choosing {{property_name}}. We\'re excited to have you as our resident.\n\nProperty Manager: {{property_manager}}\nContact: {{contact_number}}\n\nFeel free to reach out if you need anything!',
                'variables' => ['tenant_name', 'property_name', 'property_manager', 'contact_number']
            ],
            [
                'name' => 'Rent Due Reminder',
                'type' => 'whatsapp',
                'subject' => '',
                'message' => 'Hi {{tenant_name}}! 👋\n\nFriendly reminder that your rent of {{rent_amount}} for {{property_name}} is due on {{due_date}}.\n\nPlease make payment to avoid late fees. Let us know if you have any questions!\n\nThanks,\n{{property_manager}}',
                'variables' => ['tenant_name', 'rent_amount', 'property_name', 'due_date', 'property_manager']
            ],
            [
                'name' => 'Maintenance Notification',
                'type' => 'whatsapp',
                'subject' => '',
                'message' => 'Hi {{tenant_name}},\n\nQuick update about your maintenance request at {{property_name}}:\n\n{{status}}: {{details}}\n\nWe\'ll keep you informed on the progress. Contact us if you need anything!\n\n{{property_manager}}',
                'variables' => ['tenant_name', 'property_name', 'status', 'details', 'property_manager']
            ],
            [
                'name' => 'General Announcement',
                'type' => 'whatsapp',
                'subject' => '',
                'message' => 'Dear {{tenant_name}},\n\n{{announcement}}\n\nIf you have any questions, please contact us at {{contact_number}}.\n\nThank you,\n{{property_manager}}',
                'variables' => ['tenant_name', 'announcement', 'contact_number', 'property_manager']
            ]
        ];
    }
    
    /**
     * Replicate templates from one type to another
     */
    public function replicateTemplates($adminId, $fromType, $toType) {
        // Get all templates of the source type
        $sourceTemplates = $this->getTemplates($adminId, $fromType);
        
        foreach ($sourceTemplates as $template) {
            // Check if template already exists in target type
            $templates = $this->db->fetchAll(
                "SELECT id FROM message_templates WHERE admin_id = ? AND name = ? AND type = ? AND deleted_at IS NULL",
                [$adminId, $template['name'], $toType]
            );
            $existing = $templates[0] ?? null;
            
            if (!$existing) {
                // Create template for target type
                $newTemplate = [
                    'name' => $template['name'],
                    'type' => $toType,
                    'subject' => $template['subject'] ?? '',
                    'message' => $template['message'],
                    'variables' => json_decode($template['variables'] ?? '[]', true)
                ];
                
                $this->createTemplate($adminId, $newTemplate);
            }
        }
    }
    
    /**
     * Replicate all templates across all types (email, sms, whatsapp)
     */
    public function replicateAllTemplates($adminId) {
        // Get all unique template names across all types
        $allTemplates = $this->db->fetchAll(
            "SELECT DISTINCT name, message, variables, subject FROM message_templates WHERE admin_id = ? AND deleted_at IS NULL",
            [$adminId]
        );
        
        $types = ['email', 'sms', 'whatsapp'];
        
        foreach ($allTemplates as $template) {
            foreach ($types as $type) {
                // Check if this template already exists for this type
                $templates = $this->db->fetchAll(
                    "SELECT id FROM message_templates WHERE admin_id = ? AND name = ? AND type = ? AND deleted_at IS NULL",
                    [$adminId, $template['name'], $type]
                );
                $existing = $templates[0] ?? null;
                
                if (!$existing) {
                    // Create template for this type
                    $newTemplate = [
                        'name' => $template['name'],
                        'type' => $type,
                        'subject' => $template['subject'] ?? '',
                        'message' => $template['message'],
                        'variables' => json_decode($template['variables'] ?? '[]', true)
                    ];
                    
                    $this->createTemplate($adminId, $newTemplate);
                }
            }
        }
    }
    
    /**
     * Initialize default templates for admin
     */
    public function initializeDefaultTemplates($adminId) {
        $defaultTemplates = $this->getDefaultTemplates();
        
        foreach ($defaultTemplates as $template) {
            // Check if template already exists
            $templates = $this->db->fetchAll(
                "SELECT id FROM message_templates WHERE admin_id = ? AND name = ? AND type = ? AND deleted_at IS NULL",
                [$adminId, $template['name'], $template['type']]
            );
            $existing = $templates[0] ?? null;
            
            if (!$existing) {
                $this->createTemplate($adminId, $template);
            }
        }
        
        // After initializing, replicate all templates across all types
        $this->replicateAllTemplates($adminId);
    }
}
?>

# WhatsApp API Integration Setup Guide

This guide will help you set up WhatsApp Business API integration for your Cornerstone Realty communications system.

## Prerequisites

1. **Meta Business Account**: You need a verified Meta Business Account
2. **WhatsApp Business API**: Access to WhatsApp Business API
3. **Phone Number**: A dedicated phone number for WhatsApp Business

## Step 1: Get WhatsApp Business API Access

1. Go to [Meta for Developers](https://developers.facebook.com/)
2. Create a new app or use an existing one
3. Add WhatsApp product to your app
4. Complete the verification process
5. Get your phone number approved

## Step 2: Get API Credentials

Once your WhatsApp Business API is set up, you'll need:

1. **Access Token**: Permanent or temporary access token
2. **Phone Number ID**: The ID of your WhatsApp phone number
3. **Webhook URL** (optional): For receiving messages

## Step 3: Configure Environment Variables

Update your `.env` file with your WhatsApp credentials:

```env
# WhatsApp Business API Configuration
WHATSAPP_ACCESS_TOKEN=your_actual_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_actual_phone_number_id_here
WHATSAPP_API_VERSION=v18.0
```

## Step 4: Test the Integration

1. Log in to your admin dashboard
2. Go to Communications section
3. Click "New Message"
4. Select "WhatsApp" as message type
5. Select a recipient with phone number
6. Type a test message and send

## Features Available

### ✅ Implemented Features

- **Message Type Selection**: Choose between Email, SMS, and WhatsApp
- **Template System**: Use predefined message templates with variables
- **WhatsApp Templates**: Send both text messages and approved WhatsApp templates
- **Recipient Validation**: Checks if recipients have required contact info
- **Message Preview**: Preview messages with template variables
- **Multi-recipient Support**: Send messages to multiple recipients at once
- **Status Tracking**: Track sent/delivered/failed status
- **WhatsApp Message ID**: Store WhatsApp message IDs for tracking

### 🚧 TODO Features

- **Email Integration**: Connect to email service (PHPMailer/SendGrid)
- **SMS Integration**: Connect to SMS service (Twilio/Nexmo)
- **Webhook Handling**: Receive WhatsApp message status updates
- **Media Messages**: Send images/documents via WhatsApp
- **Message Scheduling**: Schedule messages for later delivery
- **Message Templates Management**: CRUD operations for templates

## WhatsApp Template Requirements

For sending messages to users who haven't messaged you first, you must use approved WhatsApp templates:

1. Templates must be pre-approved by Meta
2. They support variables with `{{variable_name}}` syntax
3. Templates have categories: Transactional, Marketing, Utility

## Common Issues and Solutions

### Issue: "WhatsApp API not configured"
**Solution**: Make sure your `.env` file has the correct WhatsApp credentials

### Issue: "Recipient does not have phone number"
**Solution**: Add phone numbers to tenant records in the database

### Issue: Template not approved
**Solution**: Submit your template for approval in Meta Business Suite

### Issue: Rate limiting
**Solution**: WhatsApp has rate limits. Wait between messages or upgrade your tier

## Security Considerations

1. **Access Token Security**: Never commit access tokens to version control
2. **Phone Number Privacy**: Protect tenant phone numbers
3. **Message Content**: Follow WhatsApp's messaging policies
4. **User Consent**: Only message users who have opted in

## Testing Commands

```bash
# Test database setup
php database/create_message_templates_table.php

# Test WhatsApp service (requires credentials)
php -r "
require_once 'services/WhatsAppService.php';
$service = new WhatsAppService();
var_dump($service->isConfigured());
"
```

## Support

For WhatsApp Business API issues:
- [Meta for Developers Documentation](https://developers.facebook.com/docs/whatsapp/)
- [WhatsApp Business API Support](https://developers.facebook.com/support/whatsapp/)

For application issues, check the logs:
```bash
tail -f logs/debugchecker_errors.log
```

## Next Steps

1. Complete the WhatsApp Business API setup
2. Update `.env` with your credentials
3. Test sending messages
4. Set up message templates
5. Configure email and SMS integrations
6. Set up webhooks for message status updates

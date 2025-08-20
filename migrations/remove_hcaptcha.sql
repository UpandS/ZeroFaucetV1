-- Remove hCaptcha settings from the database
DELETE FROM settings WHERE name IN ('hcaptcha_pub_key', 'hcaptcha_sec_key');

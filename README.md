# Law Firm Featured Cases - WordPress Plugin

A lightweight WordPress plugin developed as part of a Senior Web Developer technical assessment. This plugin allows law firms to manage and display notable legal cases using a Custom Post Type (CPT) and specific metadata.

## 🚀 Features

- **Custom Post Type**: Registers 'Featured Cases' with a clean administrative interface.
- **Custom Meta Fields**:
  - **Case Type**: A validated dropdown (Select) with 5 predefined legal categories.
  - **Settlement Amount**: A numeric field that automatically formats values to US Currency (e.g., $25,000.00) on the frontend.
- **Auto-Provisioning**: Automatically creates 3 dummy cases upon plugin activation for immediate testing.
- **Shortcode**: Easy display anywhere using `[display_featured_cases]`.
- **Security**: Full implementation of Nonces, user permission checks, and data sanitization/escaping.

## 📁 Repository Structure

The core logic of this project is located within the plugin directory:

- `/law-firm-featured-cases/`: Contains the main plugin file and logic.
  - `law-firm-featured-cases.php`: The main entry point, CPT registration, and shortcode logic.

## 🛠️ Technical Approach

- **Native Implementation**: I chose to use native WordPress functions (`register_post_type`, `add_meta_box`) instead of external plugins like ACF to demonstrate a deep understanding of the WordPress Core API and to keep the plugin dependency-free.
- **Data Integrity**: The `Settlement Amount` is stored as a raw float/number in the database. This allows for future scalability, such as sorting or filtering by value, while formatting is handled during the rendering phase.
- **Architecture**: The logic is encapsulated within a single plugin file for easy portability and to ensure that data remains available even if the theme is changed.

## 📥 Installation & Setup

### Method 1: Manual Upload (Recommended for Review)

1. Navigate to the `/law-firm-featured-cases/` folder in this repo.
2. Compress the folder into a `.zip` file.
3. In your WordPress Dashboard, go to **Plugins** > **Add New** > **Upload Plugin**.
4. Activate the plugin.

## 📝 Usage

Once activated, the plugin will automatically generate 3 dummy posts. To display them, use the following shortcode in any page or post:
`[display_featured_cases]`

## Screenshots

## 📝 Specifications

- **PHP Version**: 7.4+ recommended.
- **WordPress**: Tested on 6.0+.
- **No Styling Required**: The output uses basic HTML structures, making it easy to style via any active theme's CSS.

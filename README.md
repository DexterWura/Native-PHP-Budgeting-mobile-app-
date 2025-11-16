# Budget Tracker - Native PHP Mobile App

A production-ready, secure budgeting application built with native PHP using OOP principles. Features a beautiful iOS-style mobile interface for tracking income, expenses, budgets, and financial analytics.

## Features

- **User Authentication**: Secure registration and login with JWT tokens
- **Income Management**: Track multiple income streams (weekly, monthly, yearly)
- **Budget Management**: Set and monitor budgets by category and period
- **Transaction Tracking**: Record income and expenses with categories
- **Analytics & Charts**: Visual insights with spending trends and category breakdowns
- **iOS Design Language**: Beautiful, modern mobile-first interface
- **Production Ready**: Secure, optimized, and scalable architecture

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- PDO MySQL extension

## Installation

1. **Clone or download the project**

2. **Create the database**:
   ```sql
   mysql -u root -p < database/schema.sql
   ```

3. **Configure database connection**:
   Edit `config/database.php` and update:
   ```php
   private $host = 'localhost';
   private $db_name = 'budget_app';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

4. **Configure security**:
   Edit `config/config.php` and change:
   ```php
   define('JWT_SECRET', 'your-very-secure-secret-key-here');
   ```
   **Important**: Use a strong, random secret key in production!

5. **Set up web server**:
   - Point your web server document root to the project directory
   - Ensure `.htaccess` is enabled (for Apache)
   - For Nginx, configure URL rewriting accordingly

6. **Set permissions** (if needed):
   ```bash
   mkdir -p logs
   chmod 755 logs
   ```

## Usage

1. Open the application in your browser
2. Create an account or login
3. Start by adding income streams
4. Set up budgets for different categories
5. Record transactions as you spend
6. View analytics and insights on the dashboard

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user

### Income
- `GET /api/income` - Get all income streams
- `POST /api/income` - Create income stream
- `PUT /api/income/{id}` - Update income stream
- `DELETE /api/income/{id}` - Delete income stream

### Budgets
- `GET /api/budgets` - Get all budgets
- `POST /api/budgets` - Create budget
- `PUT /api/budgets/{id}` - Update budget
- `DELETE /api/budgets/{id}` - Delete budget
- `GET /api/budgets/status` - Get budget status

### Transactions
- `GET /api/transactions` - Get all transactions
- `POST /api/transactions` - Create transaction
- `PUT /api/transactions/{id}` - Update transaction
- `DELETE /api/transactions/{id}` - Delete transaction

### Analytics
- `GET /api/analytics/summary` - Get financial summary
- `GET /api/analytics/categories` - Get spending by category
- `GET /api/analytics/trends` - Get monthly trends

All protected endpoints require `Authorization: Bearer {token}` header.

## Security Features

- Password hashing with bcrypt (cost factor 12)
- JWT token authentication
- Prepared statements (SQL injection prevention)
- Input validation and sanitization
- CORS headers configuration
- Security headers (X-Content-Type-Options, X-Frame-Options, etc.)
- Error logging (no sensitive data exposed)

## Architecture

- **MVC Pattern**: Separation of concerns
- **OOP Principles**: Classes for Auth, Income, Budget, Transaction, Analytics
- **RESTful API**: Clean API design
- **Mobile-First**: Responsive iOS-style UI
- **Production Ready**: Error handling, logging, security best practices

## Mobile App Deployment (Android)

This app is configured as a Progressive Web App (PWA) and can be packaged as a native Android app for Google Play Store.

### PWA Features

- **Service Worker**: Offline functionality and caching
- **Web App Manifest**: Installable on mobile devices
- **Mobile Optimizations**: Touch gestures, pull-to-refresh prevention
- **App Icons**: Support for all device sizes

### Building Android App

#### Prerequisites
- Android Studio ([download](https://developer.android.com/studio))
- Java JDK 8 or higher
- Your PHP backend running (localhost for testing, HTTPS for production)

#### Quick Start for Testing

1. **Update API URL for Testing**:
   - For Android Emulator: Edit `android/app/build.gradle` and set:
     ```gradle
     buildConfigField "String", "APP_URL", '"http://10.0.2.2"'
     ```
   - For Physical Device: Replace `10.0.2.2` with your computer's local IP address
     - Find your IP: `ipconfig` (Windows) or `ifconfig` (Mac/Linux)
     - Example: `"http://192.168.1.100"`

2. **Start Your PHP Server**:
   ```bash
   # Make sure your PHP backend is running on localhost
   ```

3. **Build the APK**:
   - **Option A - Android Studio**: Open `android` folder → Build → Build APK(s)
   - **Option B - Command Line**: `cd android && ./gradlew assembleDebug`
   - APK location: `android/app/build/outputs/apk/debug/app-debug.apk`

4. **Install and Test**:
   ```bash
   # Install on emulator or device
   adb install android/app/build/outputs/apk/debug/app-debug.apk
   ```

#### Method 1: Using Capacitor (Recommended)

1. **Install dependencies**:
   ```bash
   npm install -g @capacitor/cli
   npm install @capacitor/core @capacitor/android
   ```

2. **Initialize Capacitor**:
   ```bash
   npx cap init "Budget Tracker" "com.budgettracker.app"
   npx cap add android
   ```

3. **Update production URL** in `capacitor.config.json`:
   ```json
   {
     "server": {
       "hostname": "your-production-domain.com"
     }
   }
   ```

4. **Build and open in Android Studio**:
   ```bash
   npx cap sync android
   npx cap open android
   ```

5. **Generate signed APK/AAB**:
   - In Android Studio: **Build** → **Generate Signed Bundle / APK**
   - Choose **Android App Bundle** (for Play Store)
   - Create keystore: `keytool -genkey -v -keystore budget-tracker-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias budget-tracker`

#### Method 2: Using Android Studio Directly

1. Open `android` folder in Android Studio
2. Update `MainActivity.java` with your production URL
3. Update app icons in `res/mipmap-*` folders
4. Build → Generate Signed Bundle / APK

#### Build Commands

```bash
# Debug APK (for testing)
cd android && ./gradlew assembleDebug

# Release APK
cd android && ./gradlew assembleRelease

# Release AAB (for Play Store)
cd android && ./gradlew bundleRelease
```

#### Troubleshooting

- **Can't connect to API**: Check that your PHP server is running and accessible
- **Build fails**: Make sure Android SDK is properly installed
- **App crashes**: Check logcat: `adb logcat | grep BudgetTracker`
- **For Production**: Update `APP_URL` in `build.gradle` to your production domain

### App Icons

Create icons in these sizes:
- **PWA**: 192x192px, 512x512px (place in `icons/` folder)
- **Android**: Use [Android Asset Studio](https://romannurik.github.io/AndroidAssetStudio/icons-launcher.html) to generate:
  - `mipmap-mdpi/ic_launcher.png` - 48x48
  - `mipmap-hdpi/ic_launcher.png` - 72x72
  - `mipmap-xhdpi/ic_launcher.png` - 96x96
  - `mipmap-xxhdpi/ic_launcher.png` - 144x144
  - `mipmap-xxxhdpi/ic_launcher.png` - 192x192

### Google Play Store Submission

#### Required Assets
- App icon (512x512px PNG)
- Feature graphic (1024x500px)
- Phone screenshots (2-8 screenshots, 16:9 or 9:16)
- Short description (80 characters max)
- Full description (4000 characters max)
- Privacy Policy URL (see `PRIVACY_POLICY.md` template)

#### Submission Checklist
- [ ] App signed with release keystore
- [ ] App icons set
- [ ] Privacy Policy URL added
- [ ] Screenshots uploaded
- [ ] App description complete
- [ ] Content rating completed
- [ ] Tested on multiple devices
- [ ] Backend uses HTTPS
- [ ] Version code and name set

#### Submission Steps
1. Create Google Play Developer account ($25 one-time fee)
2. Create new app in Play Console
3. Complete store listing (graphics, description)
4. Complete content rating questionnaire
5. Add Privacy Policy URL
6. Upload signed AAB file
7. Submit for review (1-3 days)

#### Version Updates
When updating:
1. Increment `versionCode` in `android/app/build.gradle`
2. Update `versionName` (e.g., "1.0.1")
3. Rebuild: `./gradlew bundleRelease`
4. Upload new AAB to Play Console

## Browser Support

- Modern mobile browsers (iOS Safari, Chrome Mobile)
- Desktop browsers (Chrome, Firefox, Safari, Edge)
- Installable as PWA on all modern browsers
- Native Android app via Capacitor/Android Studio

## License

This project is open source and available for personal and commercial use.

## Project Structure

```
├── api/                 # API endpoints
├── config/              # Configuration files
├── core/                 # Core classes (Auth, Income, Budget, etc.)
├── database/             # Database schema
├── android/              # Android app configuration
├── icons/                # App icons
├── logs/                 # Error logs
├── index.php             # Main frontend file
├── manifest.json         # PWA manifest
├── sw.js                 # Service worker
└── .htaccess            # Apache configuration
```

## Support

For issues or questions, please check the code comments or create an issue in the repository.

## Privacy Policy

A privacy policy is **required** for Play Store submission. See `PRIVACY_POLICY.md` for a template that you can customize and host on your website.

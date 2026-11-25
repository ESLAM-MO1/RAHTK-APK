abstract final class AppConfig {
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://localhost/api/v1',
  );

  static const Duration connectTimeout = Duration(seconds: 15);
  static const Duration receiveTimeout = Duration(seconds: 20);
  static const int defaultPageSize = 20;
  static const int maxPageSize = 100;
  static const String locale = 'ar';
  static const String currencyCode = 'SAR';
  static const double vatRate = 0.15;
}

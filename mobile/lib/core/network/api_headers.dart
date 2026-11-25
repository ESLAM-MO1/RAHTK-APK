import 'package:dio/dio.dart';

abstract final class ApiHeaders {
  static const String idempotency = 'Idempotency-Key';
  static const String requestId = 'X-Request-Id';
  static const String acceptLanguage = 'Accept-Language';
  static const String bearer = 'Bearer';

  static Options idempotent(String key) => Options(
        headers: <String, dynamic>{idempotency: key},
      );
}

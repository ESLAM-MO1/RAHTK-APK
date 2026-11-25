import 'package:dio/dio.dart';

import '../config/app_config.dart';

Dio buildDio({required String baseUrl, String? token}) {
  final dio = Dio(
    BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: AppConfig.connectTimeout,
      receiveTimeout: AppConfig.receiveTimeout,
      contentType: 'application/json',
      responseType: ResponseType.json,
      headers: <String, dynamic>{
        'Accept': 'application/json',
        'Accept-Language': AppConfig.locale,
        if (token != null) 'Authorization': 'Bearer $token',
      },
      validateStatus: (int? status) => status != null && status < 400,
    ),
  );

  return dio;
}

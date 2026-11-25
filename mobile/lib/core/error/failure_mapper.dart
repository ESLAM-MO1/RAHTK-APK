import 'package:dio/dio.dart';

import '../localization/strings.dart';
import 'app_failure.dart';

final class FailureMapper {
  const FailureMapper();

  AppFailure map(Object error) {
    if (error is DioException) {
      return _fromDio(error);
    }

    if (error is AppFailure) {
      return error;
    }

    return const ServerFailure(
      code: 'unexpected',
      message: Strings.unexpectedError,
    );
  }

  AppFailure _fromDio(DioException error) {
    return switch (error.type) {
      DioExceptionType.connectionTimeout ||
      DioExceptionType.sendTimeout ||
      DioExceptionType.receiveTimeout ||
      DioExceptionType.transformTimeout =>
        const TimeoutFailure(),
      DioExceptionType.connectionError => const NetworkFailure(),
      DioExceptionType.cancel => const ServerFailure(
          code: 'cancelled',
          message: Strings.requestCancelled,
        ),
      DioExceptionType.badCertificate => const ServerFailure(
          code: 'bad_certificate',
          message: Strings.badCertificate,
        ),
      DioExceptionType.badResponse => _fromResponse(error.response),
      DioExceptionType.unknown => const NetworkFailure(),
    };
  }

  AppFailure _fromResponse(Response<dynamic>? response) {
    if (response == null) {
      return const ServerFailure(
        code: 'no_response',
        message: Strings.noResponse,
      );
    }

    final status = response.statusCode ?? 0;
    final body = response.data;
    final errorBody = body is Map<String, dynamic> ? body['error'] : null;

    if (errorBody is! Map<String, dynamic>) {
      return ServerFailure(
        code: 'http_$status',
        message: Strings.requestFailed,
      );
    }

    if (status == 401) {
      return const UnauthenticatedFailure();
    }

    final code = errorBody['code'] as String? ?? 'http_$status';
    final message = errorBody['message'] as String? ?? Strings.requestFailed;
    final details = errorBody['details'];

    if (status == 422 && details is Map<String, dynamic>) {
      final fields = <String, List<String>>{};

      for (final entry in details.entries) {
        final value = entry.value;

        if (value is List) {
          fields[entry.key] =
              value.map((dynamic item) => item.toString()).toList();
        }
      }

      return ValidationFailure(message: message, fields: fields);
    }

    return ServerFailure(
      code: code,
      message: message,
      details: details is Map<String, dynamic> ? details : null,
    );
  }
}

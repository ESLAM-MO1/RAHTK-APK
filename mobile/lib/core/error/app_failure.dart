import '../localization/strings.dart';

sealed class AppFailure implements Exception {
  const AppFailure({required this.code, required this.message});

  final String code;
  final String message;

  @override
  String toString() => '$runtimeType($code): $message';
}

final class NetworkFailure extends AppFailure {
  const NetworkFailure() : super(code: 'network_unavailable', message: Strings.networkUnavailable);
}

final class TimeoutFailure extends AppFailure {
  const TimeoutFailure() : super(code: 'request_timeout', message: Strings.requestTimeout);
}

final class ServerFailure extends AppFailure {
  const ServerFailure({required super.code, required super.message, this.details});

  final Map<String, dynamic>? details;
}

final class ValidationFailure extends AppFailure {
  const ValidationFailure({required super.message, required this.fields});

  final Map<String, List<String>> fields;
}

final class UnauthenticatedFailure extends AppFailure {
  const UnauthenticatedFailure() : super(code: 'unauthenticated', message: Strings.sessionExpired);
}

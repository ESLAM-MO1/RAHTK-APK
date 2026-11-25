import '../localization/strings.dart';

abstract final class Money {
  static const int halalaPerRiyal = 100;
  static const String currencySymbol = Strings.currencySymbol;

  static int toHalala(String riyals) {
    final normalized = riyals.replaceAll(',', '').trim();
    final parts = normalized.split('.');
    final whole = int.tryParse(parts.first) ?? 0;
    final fractionText =
        parts.length > 1 ? parts[1].padRight(2, '0').substring(0, 2) : '00';
    final fraction = int.tryParse(fractionText) ?? 0;

    return (whole * halalaPerRiyal) + fraction;
  }

  static String format(int halala, {bool withSymbol = true}) {
    final negative = halala < 0;
    final absolute = halala.abs();
    final whole = absolute ~/ halalaPerRiyal;
    final fraction = absolute % halalaPerRiyal;

    final grouped = whole.toString().replaceAllMapped(
          RegExp(r'(\d)(?=(\d{3})+$)'),
          (Match match) => '${match[1]},',
        );

    final amount = '$grouped.${fraction.toString().padLeft(2, '0')}';

    if (!withSymbol) {
      return negative ? '-$amount' : amount;
    }

    return negative ? '-$amount $currencySymbol' : '$amount $currencySymbol';
  }

  static int vatOf(int netHalala, {double rate = 0.15}) =>
      (netHalala * rate).round();

  static int totalWithVat(int netHalala, {double rate = 0.15}) =>
      netHalala + vatOf(netHalala, rate: rate);
}

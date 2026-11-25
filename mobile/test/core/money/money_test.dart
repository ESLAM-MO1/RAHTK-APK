import 'package:flutter_test/flutter_test.dart';
import 'package:rahtk_mobile/core/localization/strings.dart';
import 'package:rahtk_mobile/core/money/money.dart';

void main() {
  group('Money.toHalala', () {
    test('parses a whole riyal amount', () {
      expect(Money.toHalala('120'), 12000);
    });

    test('parses two decimal places', () {
      expect(Money.toHalala('120.50'), 12050);
    });

    test('pads a single decimal place', () {
      expect(Money.toHalala('120.5'), 12050);
    });

    test('ignores digit grouping', () {
      expect(Money.toHalala('1,250.75'), 125075);
    });
  });

  group('Money.format', () {
    test('formats with thousands separator and symbol', () {
      expect(Money.format(125075), '1,250.75 ${Strings.currencySymbol}');
    });

    test('keeps two fraction digits', () {
      expect(Money.format(12000), '120.00 ${Strings.currencySymbol}');
    });

    test('formats without symbol when asked', () {
      expect(Money.format(12345, withSymbol: false), '123.45');
    });

    test('keeps the sign for negative amounts', () {
      expect(Money.format(-5000, withSymbol: false), '-50.00');
    });
  });

  group('Money.vatOf', () {
    test('applies the standard rate', () {
      expect(Money.vatOf(10000), 1500);
    });

    test('rounds to the nearest halala', () {
      expect(Money.vatOf(999), 150);
    });
  });

  group('Money.totalWithVat', () {
    test('adds vat on top of the net amount', () {
      expect(Money.totalWithVat(10000), 11500);
    });
  });

  test('round trips through formatting', () {
    const value = 987654;

    expect(Money.toHalala(Money.format(value, withSymbol: false)), value);
  });
}

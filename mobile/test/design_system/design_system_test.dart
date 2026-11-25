import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:rahtk_mobile/app.dart';
import 'package:rahtk_mobile/core/design_system/tokens.dart';
import 'package:rahtk_mobile/core/localization/strings.dart';
import 'package:rahtk_mobile/features/wallet/presentation/wallet_balance_card.dart';

Widget wrap(Widget child) => MaterialApp(
      theme: AppTheme.light(),
      home: Scaffold(body: child),
    );

void main() {
  test('light and dark themes enable material 3', () {
    expect(AppTheme.light().useMaterial3, isTrue);
    expect(AppTheme.dark().useMaterial3, isTrue);
  });

  test('typography never drops below the readable minimum', () {
    final sizes = <double?>[
      AppTypography.display.fontSize,
      AppTypography.headline.fontSize,
      AppTypography.titleLarge.fontSize,
      AppTypography.title.fontSize,
      AppTypography.bodyLarge.fontSize,
      AppTypography.body.fontSize,
      AppTypography.label.fontSize,
      AppTypography.caption.fontSize,
    ];

    for (final size in sizes) {
      expect(size, isNotNull);
      expect(size!, greaterThanOrEqualTo(12));
    }
  });

  test('spacing tokens sit on the four point grid', () {
    for (final value in <double>[
      AppSpacing.x1,
      AppSpacing.x2,
      AppSpacing.x3,
      AppSpacing.x4,
      AppSpacing.x5,
      AppSpacing.x6,
      AppSpacing.x8,
      AppSpacing.x10,
      AppSpacing.x12,
      AppSpacing.x16,
    ]) {
      expect(value % 4, 0);
    }
  });

  test('touch target meets the accessibility minimum', () {
    expect(AppSize.touchTarget, greaterThanOrEqualTo(44));
  });

  test('primary button height is reachable', () {
    expect(AppSize.buttonHeight, greaterThanOrEqualTo(AppSize.touchTarget));
  });

  testWidgets('wallet card renders a formatted balance',
      (WidgetTester tester) async {
    await tester
        .pumpWidget(wrap(const WalletBalanceCard(balanceHalala: 125075)));

    expect(find.text(Strings.walletBalance), findsOneWidget);
    expect(find.text('1,250.75 ${Strings.currencySymbol}'), findsOneWidget);
  });

  testWidgets('wallet card renders a skeleton while loading',
      (WidgetTester tester) async {
    await tester.pumpWidget(wrap(const WalletBalanceCard(isLoading: true)));

    expect(find.text('0.00 ${Strings.currencySymbol}'), findsNothing);
  });

  testWidgets('wallet card exposes the top up action when provided',
      (WidgetTester tester) async {
    var taps = 0;

    await tester.pumpWidget(
      wrap(
        WalletBalanceCard(
          balanceHalala: 5000,
          onTopUp: () => taps++,
        ),
      ),
    );

    await tester.tap(find.text(Strings.topUp));
    await tester.pump();

    expect(taps, 1);
  });

  testWidgets('app boots into the home screen', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: RahtkApp()));
    await tester.pumpAndSettle();

    expect(find.text(Strings.ourServices), findsOneWidget);
  });
}

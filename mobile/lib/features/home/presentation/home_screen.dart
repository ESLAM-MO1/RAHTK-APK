import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../../core/design_system/components/section_header.dart';
import '../../../core/design_system/tokens.dart';
import '../../../core/localization/strings.dart';
import '../../../core/routing/app_router.dart';
import '../../wallet/presentation/wallet_balance_card.dart';

final class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  static const List<({IconData icon, String label})> services = <({IconData icon, String label})>[
    (icon: Icons.receipt_long_outlined, label: Strings.labelInvoicing),
    (icon: Icons.local_shipping_outlined, label: Strings.labelShipping),
    (icon: Icons.work_outline, label: Strings.labelFreelance),
    (icon: Icons.gavel_outlined, label: Strings.labelAuctions),
    (icon: Icons.delivery_dining_outlined, label: Strings.labelDelivery),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(Strings.appName),
        backgroundColor: AppColors.surface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        titleTextStyle: AppTypography.titleLarge.copyWith(color: AppColors.textPrimary),
        actions: <Widget>[
          IconButton(
            onPressed: () => context.push(AppRoutes.notifications),
            tooltip: Strings.notifications,
            icon: const Icon(Icons.notifications_none, color: AppColors.textPrimary),
          ),
          IconButton(
            onPressed: () => context.push(AppRoutes.settings),
            tooltip: Strings.settings,
            icon: const Icon(Icons.settings_outlined, color: AppColors.textPrimary),
          ),
          const SizedBox(width: AppSpacing.x2),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.only(bottom: AppSpacing.x8),
        children: <Widget>[
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: AppSpacing.x4),
            child: WalletBalanceCard(),
          ),
          SectionHeader(
            title: Strings.ourServices,
            actionLabel: Strings.viewAll,
            onAction: () => context.push(AppRoutes.services),
          ),
          const _ServiceGrid(),
        ],
      ),
    );
  }
}

final class _ServiceGrid extends StatelessWidget {
  const _ServiceGrid();

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.x4),
      child: Wrap(
        spacing: AppSpacing.x3,
        runSpacing: AppSpacing.x3,
        children: HomeScreen.services.map((({IconData icon, String label}) service) {
          return SizedBox(
            width: 96,
            child: InkWell(
              onTap: () {},
              borderRadius: BorderRadius.circular(AppRadius.container),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: AppSpacing.x4),
                decoration: BoxDecoration(
                  color: AppColors.surfaceRaised,
                  borderRadius: BorderRadius.circular(AppRadius.container),
                  border: Border.all(color: AppColors.border),
                ),
                child: Column(
                  children: <Widget>[
                    Icon(service.icon, size: AppSize.iconLarge, color: AppColors.primary),
                    const SizedBox(height: AppSpacing.x2),
                    Text(service.label, style: AppTypography.caption),
                  ],
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }
}

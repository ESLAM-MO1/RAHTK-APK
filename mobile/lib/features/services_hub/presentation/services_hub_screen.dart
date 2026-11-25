import 'package:flutter/material.dart';

import '../../../core/design_system/components/section_header.dart';
import '../../../core/design_system/tokens.dart';
import '../../../core/localization/strings.dart';

final class ServicesHubScreen extends StatelessWidget {
  const ServicesHubScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(Strings.navServices),
        backgroundColor: AppColors.surface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        titleTextStyle: AppTypography.titleLarge.copyWith(
          color: AppColors.textPrimary,
        ),
      ),
      body: ListView(
        children: const <Widget>[
          SectionHeader(title: Strings.platforms),
          _PlatformTile(
            title: Strings.invoicing,
            subtitle: Strings.invoicingBrief,
            icon: Icons.receipt_long_outlined,
          ),
          _PlatformTile(
            title: Strings.shipping,
            subtitle: Strings.shippingBrief,
            icon: Icons.local_shipping_outlined,
          ),
          _PlatformTile(
            title: Strings.freelance,
            subtitle: Strings.freelanceBrief,
            icon: Icons.work_outline,
          ),
          _PlatformTile(
            title: Strings.auctions,
            subtitle: Strings.auctionsBrief,
            icon: Icons.gavel_outlined,
          ),
          _PlatformTile(
            title: Strings.delivery,
            subtitle: Strings.deliveryBrief,
            icon: Icons.delivery_dining_outlined,
          ),
        ],
      ),
    );
  }
}

final class _PlatformTile extends StatelessWidget {
  const _PlatformTile({
    required this.title,
    required this.subtitle,
    required this.icon,
  });

  final String title;
  final String subtitle;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.x4,
        vertical: AppSpacing.x1,
      ),
      child: Material(
        color: AppColors.surfaceRaised,
        borderRadius: BorderRadius.circular(AppRadius.container),
        child: InkWell(
          onTap: () {},
          borderRadius: BorderRadius.circular(AppRadius.container),
          child: Container(
            constraints: const BoxConstraints(minHeight: 72),
            padding: const EdgeInsets.all(AppSpacing.x4),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(AppRadius.container),
              border: Border.all(color: AppColors.border),
            ),
            child: Row(
              children: <Widget>[
                Icon(icon, size: AppSize.iconLarge, color: AppColors.primary),
                const SizedBox(width: AppSpacing.x3),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      Text(title, style: AppTypography.title),
                      const SizedBox(height: AppSpacing.x1),
                      Text(
                        subtitle,
                        style: AppTypography.caption
                            .copyWith(color: AppColors.textSecondary),
                      ),
                    ],
                  ),
                ),
                const Icon(Icons.chevron_left, color: AppColors.textDisabled),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

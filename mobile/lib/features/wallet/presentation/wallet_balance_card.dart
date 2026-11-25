import 'package:flutter/material.dart';

import '../../../core/design_system/tokens.dart';
import '../../../core/localization/strings.dart';
import '../../../core/money/money.dart';

final class WalletBalanceCard extends StatelessWidget {
  const WalletBalanceCard({
    this.balanceHalala = 0,
    this.isLoading = false,
    this.onTopUp,
    super.key,
  });

  final int balanceHalala;
  final bool isLoading;
  final VoidCallback? onTopUp;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.x5),
      decoration: BoxDecoration(
        color: AppColors.primary,
        borderRadius: BorderRadius.circular(AppRadius.container),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Row(
            children: <Widget>[
              const Icon(
                Icons.account_balance_wallet_outlined,
                size: AppSize.iconMedium,
                color: AppColors.onPrimary,
              ),
              const SizedBox(width: AppSpacing.x2),
              Text(
                Strings.walletBalance,
                style: AppTypography.label.copyWith(color: AppColors.onPrimary),
              ),
              const Spacer(),
              if (onTopUp != null)
                TextButton(
                  onPressed: onTopUp,
                  style: TextButton.styleFrom(
                    minimumSize: const Size(AppSize.touchTarget, AppSize.touchTarget),
                    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.x2),
                    foregroundColor: AppColors.onPrimary,
                    textStyle: AppTypography.label,
                  ),
                  child: const Text(Strings.topUp),
                ),
            ],
          ),
          const SizedBox(height: AppSpacing.x3),
          if (isLoading)
            Container(
              width: 140,
              height: 32,
              decoration: BoxDecoration(
                color: AppColors.onPrimary.withValues(alpha: 0.24),
                borderRadius: BorderRadius.circular(AppRadius.control),
              ),
            )
          else
            Text(
              Money.format(balanceHalala),
              style: AppTypography.display.copyWith(
                color: AppColors.onPrimary,
                fontFamily: AppTypography.numericFamily,
              ),
            ),
        ],
      ),
    );
  }
}

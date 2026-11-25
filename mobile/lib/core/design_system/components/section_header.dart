import 'package:flutter/material.dart';

import '../../../core/design_system/tokens.dart';

final class SectionHeader extends StatelessWidget {
  const SectionHeader({
    required this.title,
    this.actionLabel,
    this.onAction,
    super.key,
  });

  final String title;
  final String? actionLabel;
  final VoidCallback? onAction;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(
        left: AppSpacing.x4,
        right: AppSpacing.x4,
        top: AppSpacing.x6,
        bottom: AppSpacing.x3,
      ),
      child: Row(
        children: <Widget>[
          Expanded(
            child: Text(
              title,
              style: AppTypography.title,
              textAlign: TextAlign.start,
            ),
          ),
          if (actionLabel != null)
            TextButton(
              onPressed: onAction,
              style: TextButton.styleFrom(
                minimumSize:
                    const Size(AppSize.touchTarget, AppSize.touchTarget),
                padding: const EdgeInsets.symmetric(horizontal: AppSpacing.x2),
                foregroundColor: AppColors.primary,
                textStyle: AppTypography.label,
              ),
              child: Text(actionLabel!),
            ),
        ],
      ),
    );
  }
}

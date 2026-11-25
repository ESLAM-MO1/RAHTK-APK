import 'package:flutter/material.dart';

abstract final class AppColors {
  static const Color primary = Color(0xFF0F5C4A);
  static const Color onPrimary = Color(0xFFFFFFFF);
  static const Color primaryContainer = Color(0xFFD3E9E2);
  static const Color onPrimaryContainer = Color(0xFF06231C);
  static const Color accent = Color(0xFF9A5B18);
  static const Color onAccent = Color(0xFFFFFFFF);
  static const Color surface = Color(0xFFFBFBFA);
  static const Color surfaceRaised = Color(0xFFFFFFFF);
  static const Color surfaceSunken = Color(0xFFF2F3F1);
  static const Color border = Color(0xFFDFE2DD);
  static const Color borderStrong = Color(0xFFC2C7BF);
  static const Color textPrimary = Color(0xFF17201C);
  static const Color textSecondary = Color(0xFF5A655F);
  static const Color textDisabled = Color(0xFF98A29C);
  static const Color success = Color(0xFF1B7A3D);
  static const Color successContainer = Color(0xFFDCF2E3);
  static const Color warning = Color(0xFF8A5A00);
  static const Color warningContainer = Color(0xFFFBEED2);
  static const Color error = Color(0xFFB3261E);
  static const Color errorContainer = Color(0xFFFADAD7);
  static const Color info = Color(0xFF1F5A8F);
  static const Color infoContainer = Color(0xFFDCEAF6);
  static const Color overlay = Color(0xFF0B1410);
}

abstract final class AppColorsDark {
  static const Color primary = Color(0xFF6FCDB3);
  static const Color onPrimary = Color(0xFF04211A);
  static const Color primaryContainer = Color(0xFF12463A);
  static const Color onPrimaryContainer = Color(0xFFD3E9E2);
  static const Color accent = Color(0xFFE5A868);
  static const Color onAccent = Color(0xFF2A1705);
  static const Color surface = Color(0xFF101412);
  static const Color surfaceRaised = Color(0xFF171C19);
  static const Color surfaceSunken = Color(0xFF0A0D0C);
  static const Color border = Color(0xFF2A322D);
  static const Color borderStrong = Color(0xFF3D4741);
  static const Color textPrimary = Color(0xFFE8EDE9);
  static const Color textSecondary = Color(0xFFA2ADA6);
  static const Color textDisabled = Color(0xFF6B756F);
  static const Color success = Color(0xFF68C48A);
  static const Color successContainer = Color(0xFF133A22);
  static const Color warning = Color(0xFFE0B45E);
  static const Color warningContainer = Color(0xFF3A2A08);
  static const Color error = Color(0xFFF2857C);
  static const Color errorContainer = Color(0xFF42120F);
  static const Color info = Color(0xFF7FB3E0);
  static const Color infoContainer = Color(0xFF12293D);
  static const Color overlay = Color(0xFF000000);
}

abstract final class AppTypography {
  static const String arabicFamily = 'IBMPlexSansArabic';
  static const String numericFamily = 'Manrope';

  static const TextStyle display = TextStyle(
    fontSize: 32,
    height: 1.2500,
    fontWeight: FontWeight.w700,
    letterSpacing: 0,
  );

  static const TextStyle headline = TextStyle(
    fontSize: 24,
    height: 1.3333,
    fontWeight: FontWeight.w700,
    letterSpacing: 0,
  );

  static const TextStyle titleLarge = TextStyle(
    fontSize: 22,
    height: 1.2727,
    fontWeight: FontWeight.w600,
    letterSpacing: 0,
  );

  static const TextStyle title = TextStyle(
    fontSize: 18,
    height: 1.3333,
    fontWeight: FontWeight.w600,
    letterSpacing: 0,
  );

  static const TextStyle bodyLarge = TextStyle(
    fontSize: 16,
    height: 1.5000,
    fontWeight: FontWeight.w400,
    letterSpacing: 0,
  );

  static const TextStyle body = TextStyle(
    fontSize: 14,
    height: 1.5714,
    fontWeight: FontWeight.w400,
    letterSpacing: 0,
  );

  static const TextStyle label = TextStyle(
    fontSize: 14,
    height: 1.4286,
    fontWeight: FontWeight.w500,
    letterSpacing: 0,
  );

  static const TextStyle caption = TextStyle(
    fontSize: 12,
    height: 1.5000,
    fontWeight: FontWeight.w400,
    letterSpacing: 0,
  );

  static const TextStyle micro = TextStyle(
    fontSize: 11,
    height: 1.4545,
    fontWeight: FontWeight.w500,
    letterSpacing: 0.2,
  );
}

abstract final class AppSpacing {
  static const double x1 = 4;
  static const double x2 = 8;
  static const double x3 = 12;
  static const double x4 = 16;
  static const double x5 = 20;
  static const double x6 = 24;
  static const double x8 = 32;
  static const double x10 = 40;
  static const double x12 = 48;
  static const double x16 = 64;
}

abstract final class AppRadius {
  static const double control = 8;
  static const double container = 12;
  static const double sheet = 16;
}

abstract final class AppSize {
  static const double iconSmall = 16;
  static const double iconMedium = 20;
  static const double iconLarge = 24;
  static const double touchTarget = 44;
  static const double buttonHeight = 48;
  static const double fieldHeight = 52;
  static const double navbarHeight = 56;
  static const double maxContentWidth = 720;
}

abstract final class AppMotion {
  static const Duration stateChange = Duration(milliseconds: 150);
  static const Duration screenTransition = Duration(milliseconds: 250);
  static const Duration sheetEnter = Duration(milliseconds: 200);
  static const Duration errorShake = Duration(milliseconds: 120);
  static const Curve standard = Curves.easeInOut;
  static const Curve exit = Curves.easeOut;
}

abstract final class AppTheme {
  static ThemeData light() => _build(
        surface: AppColors.surface,
        surfaceRaised: AppColors.surfaceRaised,
        border: AppColors.border,
        primary: AppColors.primary,
      );

  static ThemeData dark() => _build(
        surface: AppColorsDark.surface,
        surfaceRaised: AppColorsDark.surfaceRaised,
        border: AppColorsDark.border,
        primary: AppColorsDark.primary,
      );

  static ThemeData _build({
    required Color surface,
    required Color surfaceRaised,
    required Color border,
    required Color primary,
  }) {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primary,
        brightness: surface.computeLuminance() > 0.5
            ? Brightness.light
            : Brightness.dark,
      ),
      scaffoldBackgroundColor: surface,
      fontFamily: AppTypography.arabicFamily,
      textTheme: const TextTheme(
        displayLarge: AppTypography.display,
        headlineMedium: AppTypography.headline,
        titleLarge: AppTypography.titleLarge,
        titleMedium: AppTypography.title,
        bodyLarge: AppTypography.bodyLarge,
        bodyMedium: AppTypography.body,
        labelLarge: AppTypography.label,
        bodySmall: AppTypography.caption,
        labelSmall: AppTypography.micro,
      ),
      cardTheme: CardThemeData(
        color: surfaceRaised,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.container),
          side: BorderSide(color: border),
        ),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          minimumSize: const Size.fromHeight(AppSize.buttonHeight),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.control),
          ),
          textStyle: AppTypography.label,
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surfaceRaised,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.x4,
          vertical: AppSpacing.x3,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.control),
          borderSide: BorderSide(color: border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.control),
          borderSide: BorderSide(color: border),
        ),
      ),
      visualDensity: VisualDensity.standard,
    );
  }
}

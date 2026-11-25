import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');

const source = resolve(root, 'design/tokens.json');
const dartTarget = resolve(root, 'mobile/lib/core/design_system/tokens.dart');
const cssTarget = resolve(root, 'backend/resources/css/tokens.css');

const pascal = (value) => value.charAt(0).toUpperCase() + value.slice(1);

const hexToArgb = (hex) => `0xFF${hex.replace('#', '').toUpperCase()}`;

const tokenData = JSON.parse(await readFile(source, 'utf8'));

const colorEntries = (palette) =>
  Object.entries(palette)
    .map(([key, value]) => `  static const Color ${key} = Color(${hexToArgb(value)});`)
    .join('\n');

const styleEntries = (scale) =>
  Object.entries(scale)
    .map(([key, value]) => {
      const weight = value.weight >= 700 ? 'w700' : value.weight >= 600 ? 'w600' : value.weight >= 500 ? 'w500' : 'w400';
      return `  static const TextStyle ${key} = TextStyle(fontSize: ${value.size}, height: ${(value.height / value.size).toFixed(4)}, fontWeight: FontWeight.${weight}, letterSpacing: ${value.letterSpacing});`;
    })
    .join('\n');

const spacingEntries = Object.entries(tokenData.spacing)
  .map(([key, value]) => `  static const double ${key} = ${value};`)
  .join('\n');

const dart = `import 'package:flutter/material.dart';

abstract final class AppColors {
${colorEntries(tokenData.color.light)}
}

abstract final class AppColorsDark {
${colorEntries(tokenData.color.dark)}
}

abstract final class AppTypography {
  static const String arabicFamily = '${tokenData.typography.fontFamilyArabic}';
  static const String numericFamily = '${tokenData.typography.fontFamilyNumeric}';

${styleEntries(tokenData.typography.scale)}
}

abstract final class AppSpacing {
${spacingEntries}
}

abstract final class AppRadius {
  static const double control = ${tokenData.radius.control};
  static const double container = ${tokenData.radius.container};
  static const double sheet = ${tokenData.radius.sheet};
}

abstract final class AppSize {
  static const double iconSmall = ${tokenData.size.iconSmall};
  static const double iconMedium = ${tokenData.size.iconMedium};
  static const double iconLarge = ${tokenData.size.iconLarge};
  static const double touchTarget = ${tokenData.size.touchTarget};
  static const double buttonHeight = ${tokenData.size.buttonHeight};
  static const double fieldHeight = ${tokenData.size.fieldHeight};
  static const double navbarHeight = ${tokenData.size.navbarHeight};
  static const double maxContentWidth = ${tokenData.size.maxContentWidth};
}

abstract final class AppMotion {
  static const Duration stateChange = Duration(milliseconds: ${tokenData.motion.stateChange});
  static const Duration screenTransition = Duration(milliseconds: ${tokenData.motion.screenTransition});
  static const Duration sheetEnter = Duration(milliseconds: ${tokenData.motion.sheetEnter});
  static const Duration errorShake = Duration(milliseconds: ${tokenData.motion.errorShake});
  static const Curve standard = Curves.easeInOut;
  static const Curve exit = Curves.easeOut;
}

abstract final class AppTheme {
  static ThemeData light() => _build(AppColors.surface, AppColors.surfaceRaised, AppColors.textPrimary, AppColors.primary);

  static ThemeData dark() => _build(AppColorsDark.surface, AppColorsDark.surfaceRaised, AppColorsDark.textPrimary, AppColorsDark.primary);

  static ThemeData _build(Color surface, Color surfaceRaised, Color onSurface, Color primary) {
    final scheme = ColorScheme.fromSeed(seedColor: primary, brightness: surface.computeLuminance() > 0.5 ? Brightness.light : Brightness.dark);

    return ThemeData(
      useMaterial3: true,
      colorScheme: scheme,
      scaffoldBackgroundColor: surface,
      fontFamily: AppTypography.arabicFamily,
      textTheme: TextTheme(
        displayLarge: AppTypography.display,
        headlineMedium: AppTypography.headline,
        titleLarge: AppTypography.titleLarge,
        titleMedium: AppTypography.title,
        bodyLarge: AppTypography.bodyLarge,
        bodyMedium: AppTypography.body,
        labelLarge: AppTypography.label,
        bodySmall: AppTypography.caption,
      ),
      cardTheme: CardThemeData(
        color: surfaceRaised,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.container),
          side: BorderSide(color: AppColors.border),
        ),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          minimumSize: const Size.fromHeight(AppSize.buttonHeight),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(AppRadius.control)),
          textStyle: AppTypography.label,
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surfaceRaised,
        contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.x4, vertical: AppSpacing.x3),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.control),
          borderSide: BorderSide(color: AppColors.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.control),
          borderSide: BorderSide(color: AppColors.border),
        ),
      ),
      visualDensity: VisualDensity.standard,
    );
  }
}
`;

const css = `:root {
${Object.entries(tokenData.color.light)
  .map(([key, value]) => `  --color-${key.replace(/[A-Z]/g, (m) => '-' + m.toLowerCase())}: ${value};`)
  .join('\n')}
${Object.entries(tokenData.spacing)
  .map(([key, value]) => `  --space-${key}: ${value}px;`)
  .join('\n')}
${Object.entries(tokenData.radius)
  .map(([key, value]) => `  --radius-${key}: ${value}px;`)
  .join('\n')}
  --font-arabic: '${tokenData.typography.fontFamilyArabic}', system-ui, sans-serif;
  --font-numeric: '${tokenData.typography.fontFamilyNumeric}', ui-monospace, monospace;
${Object.entries(tokenData.typography.scale)
  .map(([key, value]) => `  --text-${key.replace(/[A-Z]/g, (m) => '-' + m.toLowerCase())}-size: ${value.size}px;\n  --text-${key.replace(/[A-Z]/g, (m) => '-' + m.toLowerCase())}-height: ${value.height}px;`)
  .join('\n')}
}

[data-theme='dark'] {
${Object.entries(tokenData.color.dark)
  .map(([key, value]) => `  --color-${key.replace(/[A-Z]/g, (m) => '-' + m.toLowerCase())}: ${value};`)
  .join('\n')}
}
`;

await mkdir(dirname(dartTarget), { recursive: true });
await mkdir(dirname(cssTarget), { recursive: true });

await writeFile(dartTarget, dart, 'utf8');
await writeFile(cssTarget, css, 'utf8');

process.stdout.write(`tokens: ${Object.keys(tokenData.color.light).length} colors, ${Object.keys(tokenData.typography.scale).length} type styles\n`);
process.stdout.write(`written: ${dartTarget}\n`);
process.stdout.write(`written: ${cssTarget}\n`);

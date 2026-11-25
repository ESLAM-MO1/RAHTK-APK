import 'package:go_router/go_router.dart';

import '../../features/home/presentation/home_screen.dart';
import '../../features/services_hub/presentation/services_hub_screen.dart';

abstract final class AppRoutes {
  static const String home = '/';
  static const String services = '/services';
  static const String perks = '/perks';
  static const String wallet = '/wallet';
  static const String orders = '/orders';
  static const String notifications = '/notifications';
  static const String settings = '/settings';
}

final GoRouter appRouter = GoRouter(
  initialLocation: AppRoutes.home,
  routes: <RouteBase>[
    GoRoute(
      path: AppRoutes.home,
      name: 'home',
      builder: (context, state) => const HomeScreen(),
    ),
    GoRoute(
      path: AppRoutes.services,
      name: 'services',
      builder: (context, state) => const ServicesHubScreen(),
    ),
  ],
);

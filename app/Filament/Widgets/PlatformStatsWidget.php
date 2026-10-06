<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Post;
use App\Models\Report;
use App\Models\Trade;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeTradesCount = Trade::whereIn('status', ['proposed', 'accepted', 'shipped'])->count();
        $completedToday = Trade::where('status', 'completed')
            ->whereDate('updated_at', today())->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();
        $activeListingsCount = Listing::where('status', 'active')->count();
        $postsToday = Post::whereDate('created_at', today())->count();

        return [
            Stat::make('Trade Aktif', $activeTradesCount)
                ->description('Proposed + Accepted + Shipped')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color($activeTradesCount > 50 ? 'warning' : 'primary'),

            Stat::make('Trade Selesai Hari Ini', $completedToday)
                ->description('Completed today')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Laporan Pending', $pendingReports)
                ->description('Perlu ditinjau')
                ->descriptionIcon('heroicon-m-flag')
                ->color($pendingReports > 0 ? 'danger' : 'gray'),

            Stat::make('User Baru (7 Hari)', $newUsersThisWeek)
                ->description('Pendaftaran pekan ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Listing Aktif', $activeListingsCount)
                ->description('Di pasar saat ini')
                ->descriptionIcon('heroicon-m-tag')
                ->color('primary'),

            Stat::make('Post Hari Ini', $postsToday)
                ->description('Aktivitas komunitas')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color('gray'),
        ];
    }
}

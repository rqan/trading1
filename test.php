                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4l3 3"></path>
                </svg>
                DCastTrade
            </a>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <div class="hidden sm:flex items-center gap-1 flex-1 justify-center">
                <?php $cur = request()->path(); ?>
                <a href="<?php echo e(route('values')); ?>"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          <?php echo e(str_starts_with($cur,'values') || str_starts_with($cur,'casting') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'); ?>">
                    Values
                </a>
                <a href="<?php echo e(route('listings')); ?>"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          <?php echo e(str_starts_with($cur,'listings') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'); ?>">
                    Market
                </a>
                <a href="<?php echo e(route('clubs.index')); ?>"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          <?php echo e(str_starts_with($cur,'clubs') || str_starts_with($cur,'community') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'); ?>">
                    Clubs
                </a>
                <a href="<?php echo e(route('my.trades')); ?>"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          <?php echo e(str_starts_with($cur,'my-trades') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'); ?>">
                    My Trades
                </a>
                <a href="<?php echo e(route('my.listings')); ?>"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          <?php echo e(str_starts_with($cur,'my-listings') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'); ?>">
                    My Listings
                </a>
                <a href="<?php echo e(route('my.journals')); ?>"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          <?php echo e(str_starts_with($cur,'journals') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600'); ?>">
                    📔 Jurnal
                </a>
            </div>

            
            <div class="hidden sm:flex items-center">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('notification-bell');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1042161775-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            
            <div class="hidden sm:block relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 min-h-[44px] px-3 py-2 text-sm font-medium text-slate-600 hover:text-orange-600 transition-colors">
                    <span class="h-7 w-7 rounded-full bg-orange-100 flex items-center justify-center text-xs font-bold text-orange-700">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                    </span>
                    <span class="max-w-[100px] truncate"><?php echo e(auth()->user()->name); ?></span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition
                     class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm z-50">
                    <a href="<?php echo e(route('user.profile', auth()->id())); ?>"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        Profil Saya
                    </a>
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        Koleksi
                    </a>
                    <a href="<?php echo e(route('my.listings')); ?>"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        My Listings
                    </a>
                    <a href="<?php echo e(route('my.trades')); ?>"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        My Trades
                    </a>
                    <a href="<?php echo e(route('my.journals')); ?>"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        📔 Jurnal Saya
                    </a>
                    <a href="<?php echo e(route('dashboard')); ?>?tab=wishlist"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        ❤️ My Wishlist
                    </a>
                    <hr class="my-1 border-slate-100">
                    <a href="<?php echo e(route('profile')); ?>"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        Pengaturan
                    </a>
                    <hr class="my-1 border-slate-100">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            
            <div class="sm:hidden" x-data="{ mobileOpen: false }">
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md text-slate-600 hover:text-orange-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div x-show="mobileOpen" x-transition
                     class="absolute top-14 left-0 right-0 bg-white border-b border-slate-200 shadow-md py-2 z-50">
                    <a href="<?php echo e(route('values')); ?>" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">Values</a>
                    <a href="<?php echo e(route('listings')); ?>" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">Market</a>
                    <a href="<?php echo e(route('clubs.index')); ?>" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">Clubs</a>
                    <a href="<?php echo e(route('my.trades')); ?>" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">My Trades</a>
                    <a href="<?php echo e(route('my.listings')); ?>" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">My Listings</a>
                    <a href="<?php echo e(route('my.journals')); ?>" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">📔 Jurnal</a>
                    <a href="<?php echo e(route('dashboard')); ?>?tab=wishlist" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">❤️ Wishlist</a>
                    <hr class="my-1 border-slate-100">
                    <a href="<?php echo e(route('user.profile', auth()->id())); ?>" class="block px-5 py-3 text-sm text-slate-700 hover:bg-orange-50">Profil Saya</a>
                    <a href="<?php echo e(route('profile')); ?>" class="block px-5 py-3 text-sm text-slate-700 hover:bg-orange-50">Pengaturan</a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="px-5 py-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm text-red-600">Keluar</button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('login')); ?>" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-orange-600">Masuk</a>
                <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 text-sm font-medium bg-orange-600 text-white rounded-lg hover:bg-orange-700">Daftar</a>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </nav>

    
    <main class="max-w-4xl mx-auto px-4 py-6 min-h-[80vh]">
        <?php echo e($slot); ?>

    </main>

    
    <footer class="bg-white border-t border-slate-200 mt-12">
        <div class="max-w-5xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 font-bold text-orange-600 text-lg mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                        DCastTrade
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">Platform komunitas dan pertukaran diecast / Hot Wheels terpercaya.</p>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Aplikasi</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo e(route('values')); ?>" class="text-slate-600 hover:text-orange-600">Values</a></li>
                        <li><a href="<?php echo e(route('listings')); ?>" class="text-slate-600 hover:text-orange-600">Market</a></li>
                        <li><a href="<?php echo e(route('clubs.index')); ?>" class="text-slate-600 hover:text-orange-600">Clubs</a></li>
                        <li><a href="<?php echo e(route('my.trades')); ?>" class="text-slate-600 hover:text-orange-600">My Trades</a></li>
                        <li><a href="<?php echo e(route('my.listings')); ?>" class="text-slate-600 hover:text-orange-600">My Listings</a></li>
                        <li><a href="<?php echo e(route('notifications')); ?>" class="text-slate-600 hover:text-orange-600">Notifikasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Info</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo e(route('tentang')); ?>" class="text-slate-600 hover:text-orange-600">Tentang DCastTrade</a></li>
                        <li><a href="<?php echo e(route('aturan')); ?>" class="text-slate-600 hover:text-orange-600">Aturan Komunitas</a></li>
                        <li><a href="<?php echo e(route('privasi')); ?>" class="text-slate-600 hover:text-orange-600">Kebijakan Privasi</a></li>
                        <li><a href="mailto:support@dcasttrade.com" class="text-slate-600 hover:text-orange-600">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400">
                <span>&copy; <?php echo e(date('Y')); ?> DCastTrade. Dibuat dengan ❤️ untuk komunitas kolektor diecast Indonesia.</span>
                <div class="flex gap-3">
                    <a href="<?php echo e(route('aturan')); ?>" class="hover:text-orange-600">Aturan</a>
                    <a href="<?php echo e(route('privasi')); ?>" class="hover:text-orange-600">Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<?php /**PATH /home/focarix/GitHub/trading1/resources/views/layouts/app.blade.php ENDPATH**/ ?>
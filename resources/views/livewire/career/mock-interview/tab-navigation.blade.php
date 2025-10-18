<div class="flex flex-wrap border-b border-themed-primary mb-8">
    <button wire:click="$set('activeTab', 'dashboard')"
        class="px-6 py-3 text-lg font-medium {{ $activeTab === 'dashboard' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-themed-secondary hover:text-themed-primary' }} transition-colors">
        <i class="fas fa-th-large mr-2"></i> Dashboard
    </button>
    <button wire:click="$set('activeTab', 'interviews')"
        class="px-6 py-3 text-lg font-medium {{ $activeTab === 'interviews' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-themed-secondary hover:text-themed-primary' }} transition-colors">
        <i class="fas fa-list mr-2"></i> My Interviews
    </button>
    <button wire:click="$set('activeTab', 'practice')"
        class="px-6 py-3 text-lg font-medium {{ $activeTab === 'practice' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-themed-secondary hover:text-themed-primary' }} transition-colors">
        <i class="fas fa-play-circle mr-2"></i> Quick Practice
    </button>
    <button wire:click="$set('activeTab', 'analytics')"
        class="px-6 py-3 text-lg font-medium {{ $activeTab === 'analytics' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-themed-secondary hover:text-themed-primary' }} transition-colors">
        <i class="fas fa-chart-line mr-2"></i> Performance Analytics
    </button>
</div>
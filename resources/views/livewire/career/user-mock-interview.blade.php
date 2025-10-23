<div class="min-h-screen bg-themed-primary">
    <!-- Header Section -->
    <div class="bg-themed-secondary shadow-lg border-b border-themed-primary">
        <div class="px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-themed-primary mb-2">Mock Interviews</h1>
                    <p class="text-xl text-themed-secondary">Practice makes perfect - Ace your next interview</p>
                </div>

                <!-- Statistics Cards -->
                @include('livewire.career.mock-interview.statistics-cards', ['statistics' => $this->statistics])
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @include('livewire.career.mock-interview.flash-messages')

    <div class="px-6 py-8">
        <!-- Tab Navigation -->
        @include('livewire.career.mock-interview.tab-navigation')

        <!-- Dashboard Tab -->
        @if($activeTab === 'dashboard')
            @include('livewire.career.mock-interview.dashboard-tab')
        @endif

        <!-- Interviews Tab -->
        @if($activeTab === 'interviews')
            @include('livewire.career.mock-interview.interviews-tab')
        @endif

        <!-- Practice Tab -->
        @if($activeTab === 'practice')
            @include('livewire.career.mock-interview.practice-tab')
        @endif

        <!-- Analytics Tab -->
        @if($activeTab === 'analytics')
            @include('livewire.career.mock-interview.analytics-tab')
        @endif
    </div>

    <!-- Modals -->
    @include('livewire.career.mock-interview.create-modal')
    @include('livewire.career.mock-interview.interview-taking-modal')
    @include('livewire.career.mock-interview.results-modal')

    <!-- JavaScript -->
    @include('livewire.career.mock-interview.scripts')
</div>
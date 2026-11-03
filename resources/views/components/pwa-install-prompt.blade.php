<div
    x-data="{
        installAvailable: window.bootkodePwa?.canInstall() || false,
        updateAvailable: window.bootkodePwa?.hasUpdate() || false,
        dismissed: false,
        busy: false,
        async install() {
            this.busy = true;
            await window.bootkodePwa?.install();
            this.busy = false;
        },
        update() {
            this.busy = window.bootkodePwa?.activateUpdate() || false;
        }
    }"
    x-on:bootkode:pwa-install-state.window="installAvailable = $event.detail.available; if ($event.detail.installed) dismissed = true"
    x-on:bootkode:pwa-update-state.window="updateAvailable = $event.detail.available"
    x-show="!dismissed && (installAvailable || updateAvailable)"
    x-cloak
    class="bk-pwa-prompt"
    role="status"
    aria-live="polite"
>
    <div class="min-w-0">
        <p class="font-semibold text-themed-primary" x-text="updateAvailable ? 'BootKode update ready' : 'Install BootKode'">Install BootKode</p>
        <p class="mt-1 text-xs text-themed-secondary" x-text="updateAvailable ? 'Apply the latest offline and performance improvements.' : 'Add the learning app to this device for quicker access.'"></p>
    </div>
    <div class="flex shrink-0 items-center gap-2">
        <button type="button" class="bk-button bk-button-primary" :disabled="busy" @click="updateAvailable ? update() : install()">
            <span x-text="busy ? 'Working…' : (updateAvailable ? 'Update' : 'Install')">Install</span>
        </button>
        <button type="button" class="bk-icon-button" @click="dismissed = true" aria-label="Dismiss app prompt">
            <i class="fas fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
</div>

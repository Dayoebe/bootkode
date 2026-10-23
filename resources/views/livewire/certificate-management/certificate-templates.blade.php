<div class="space-y-6">
    <x-ui.page-header title="Certificate templates" description="Save certificate designs and choose the default for future awards. Issued certificates retain their saved design.">
        <x-slot:actions>
            @if(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']))
                <x-primary-button type="button" wire:click="showCreateModal">Create template</x-primary-button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    @error('template') <p role="alert" class="text-danger font-medium">{{ $message }}</p> @enderror

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($this->templates as $template)
            @php($settings = \App\Models\Credentials\CertificateTemplate::safeSettings($template->settings))
            <x-ui.panel wire:key="template-{{ $template->id }}">
                <div class="space-y-4">
                    <div aria-hidden="true" class="border-4 p-6 text-center" style="background-color: {{ $settings['backgroundColor'] }}; border-color: {{ $settings['borderColor'] }}; color: {{ $settings['textColor'] }}">
                        <p class="text-xs uppercase tracking-widest" style="color: {{ $settings['accentColor'] }}">Design preview</p>
                        <p class="mt-2 text-xl" style="font-family: '{{ $settings['headerFont'] }}', serif">Certificate</p>
                        <p class="mt-2 text-sm" style="font-family: '{{ $settings['bodyFont'] }}', sans-serif">Recipient name</p>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-content break-words">{{ $template->name }}</h2>
                        @if($template->is_default) <p class="text-sm font-semibold text-content">Default for future certificates</p> @endif
                        <p class="mt-1 text-sm text-content-muted break-words">{{ $template->description }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <x-secondary-button wire:click="previewTemplate({{ $template->id }})">Preview</x-secondary-button>
                        @if(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']))
                            <x-secondary-button wire:click="showEditModal({{ $template->id }})">Edit</x-secondary-button>
                            @unless($template->is_default)
                                <x-danger-button type="button" wire:click="deleteTemplate({{ $template->id }})" wire:confirm="Delete this saved design? Issued certificates keep their design." wire:loading.attr="disabled">Delete</x-danger-button>
                            @endunless
                        @endif
                        @if(!$template->is_default && auth()->user()->hasRole('super_admin'))
                            <x-secondary-button wire:click="setDefaultTemplate({{ $template->id }})" wire:confirm="Use this design for future certificate awards?" wire:loading.attr="disabled">Set default</x-secondary-button>
                        @endif
                    </div>
                </div>
            </x-ui.panel>
        @empty
            <div class="md:col-span-2 xl:col-span-3">
                <x-ui.empty-state title="No saved templates" description="The existing certificate design remains in use until a super administrator selects a saved default." />
            </div>
        @endforelse
    </div>

    <x-modal name="certificate-template-editor" model="showEditor" label="Certificate template editor">
        <form wire:submit="{{ $selectedTemplate ? 'updateTemplate' : 'saveTemplate' }}" class="space-y-5 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold">{{ $selectedTemplate ? 'Edit template' : 'Create template' }}</h2>
                <x-secondary-button wire:click="closeModals" aria-label="Close template editor">Close</x-secondary-button>
            </div>
            <div>
                <x-input-label for="template-name" value="Template name" />
                <x-text-input id="template-name" wire:model="templateName" class="mt-1 block w-full" required maxlength="100" aria-describedby="template-name-error" :aria-invalid="$errors->has('templateName') ? 'true' : 'false'" />
                <x-input-error id="template-name-error" :messages="$errors->get('templateName')" />
            </div>
            <div>
                <x-input-label for="template-description" value="Description" />
                <textarea aria-label="Template description" id="template-description" wire:model="templateDescription" rows="2" maxlength="255" class="mt-1 block w-full rounded-lg border-line bg-surface text-content" aria-describedby="template-description-error"></textarea>
                <x-input-error id="template-description-error" :messages="$errors->get('templateDescription')" />
            </div>
            <fieldset>
                <legend class="mb-2 font-semibold">Colors</legend>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach(['backgroundColor' => 'Background', 'borderColor' => 'Border', 'textColor' => 'Text', 'accentColor' => 'Accent'] as $field => $label)
                        <div>
                            <x-input-label :for="'template-'.$field" :value="$label" />
                            <input id="template-{{ $field }}" type="color" wire:model="{{ $field }}" class="mt-1 h-11 w-full cursor-pointer rounded-lg border border-line bg-surface" aria-describedby="{{ $field }}-error">
                            <x-input-error :id="$field.'-error'" :messages="$errors->get($field)" />
                        </div>
                    @endforeach
                </div>
            </fieldset>
            <fieldset>
                <legend class="mb-2 font-semibold">Typography</legend>
                <p class="mb-3 text-sm text-content-muted">These fonts are available to the PDF renderer without external downloads.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach(['headerFont' => 'Heading font', 'bodyFont' => 'Body font'] as $field => $label)
                        <div>
                            <x-input-label :for="'template-'.$field" :value="$label" />
                            <select id="template-{{ $field }}" wire:model="{{ $field }}" class="mt-1 min-h-11 w-full rounded-lg border-line bg-surface text-content" aria-describedby="{{ $field }}-error">
                                @foreach(\App\Models\Credentials\CertificateTemplate::FONTS as $font) <option value="{{ $font }}">{{ $font }}</option> @endforeach
                            </select>
                            <x-input-error :id="$field.'-error'" :messages="$errors->get($field)" />
                        </div>
                    @endforeach
                </div>
            </fieldset>
            @if(auth()->user()->hasRole('super_admin'))
                <label for="template-default" class="flex min-h-11 items-center gap-3">
                    <input id="template-default" type="checkbox" wire:model="isDefault" class="rounded border-line">
                    <span>Use as the default for future certificates</span>
                </label>
                <p class="text-sm text-content-muted">To replace an existing default, select another saved template.</p>
            @endif
            <div class="flex flex-wrap justify-end gap-3 border-t border-line pt-4">
                <x-secondary-button wire:click="closeModals">Cancel</x-secondary-button>
                <x-primary-button wire:loading.attr="disabled" wire:target="saveTemplate,updateTemplate">
                    <span wire:loading.remove wire:target="saveTemplate,updateTemplate">Save template</span>
                    <span wire:loading wire:target="saveTemplate,updateTemplate" role="status">Saving…</span>
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="certificate-template-preview" model="showPreview" label="Certificate design preview">
        <div class="space-y-4 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold">Design preview</h2>
                <x-secondary-button wire:click="closeModals">Close preview</x-secondary-button>
            </div>
            <p class="text-sm text-content-muted">Example labels show the layout. This preview is not an issued certificate.</p>
            @if($showPreview)
                <iframe title="Certificate design preview" sandbox="" srcdoc="{{ $previewHtml }}" class="h-[480px] w-full rounded-lg border border-line bg-surface"></iframe>
            @endif
        </div>
    </x-modal>
    <p wire:loading.delay wire:target="previewTemplate,setDefaultTemplate,deleteTemplate" role="status" class="text-sm text-content-muted">Updating certificate templates…</p>
</div>

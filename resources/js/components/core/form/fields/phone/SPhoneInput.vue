<template>
    <PhoneInput noUseBrowserLocale fetchCountry class="flex" :country-locale="countryLocale || 'pt-BR'"
        :ignored-countries="['AC']">
        <template #selector="{ inputValue, updateInputValue, countries }">
            <Popover v-model:open="open">
                <PopoverTrigger>
                    <Button variant="outline" class="flex gap-1 rounded-e-none rounded-s-lg px-3 py-4" type="button">
                        <FlagComponent :country="inputValue" />
                        <ChevronsUpDownIcon class="-mr-2 h-4 w-4 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-[300px] p-0">
                    <Command>
                        <CommandInput placeholder="Search country..." />
                        <CommandEmpty>No country found.</CommandEmpty>
                        <CommandList>
                            <CommandGroup>
                                <CommandItem v-for="option in countries" :key="option.iso2" :value="option.name"
                                    class="gap-2" @select="
                                        () => {
                                            updateInputValue(option.iso2)
                                            open = false
                                            focused = true
                                        }
                                    ">
                                    <FlagComponent :country="option?.iso2" />
                                    <span class="flex-1 text-sm">{{
                                        option.name
                                    }}</span>
                                    <span class="text-foreground/50 text-sm">{{
                                        option.dialCode
                                    }}</span>
                                </CommandItem>
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>
        </template>

        <template #input="{ inputValue, updateInputValue, placeholder }">
            <Input ref="phoneInput" class="rounded-e-lg rounded-s-none" type="text" :model-value="inputValue"
                @input="updateInputValue" :placeholder="placeholder" />
        </template>
    </PhoneInput>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import PhoneInput from 'base-vue-phone-input'
import { Input } from '@/components/ui/input';
import { ChevronsUpDownIcon } from 'lucide-vue-next'
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import FlagComponent from './FlagComponent.vue'
import { useFocus } from '@vueuse/core'
defineProps<{
    countryLocale: string 
}>();
const open = ref(false)
const phoneInput = ref(null)
const { focused } = useFocus(phoneInput)
</script>

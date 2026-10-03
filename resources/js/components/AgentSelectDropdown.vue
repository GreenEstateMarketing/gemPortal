<template>
  <div class="multi-select" :class="'multi-select--' + variant" ref="root">
    <label class="multi-select__field-label" v-if="variant === 'boxed'">{{ label }}</label>
    <button type="button" class="multi-select__toggle" @click="toggle">
      <span class="multi-select__toggle-label">{{ toggleLabel }}</span>
      <i class="fas fa-chevron-down multi-select__chevron" :class="{ 'multi-select__chevron--open': open }"></i>
    </button>

    <div class="multi-select__panel" v-if="open">
      <input
        v-if="options.length > 6"
        type="text"
        class="multi-select__search"
        v-model="query"
        :placeholder="'Search ' + label.toLowerCase()"
      />
      <div class="multi-select__list">
        <button
          type="button"
          class="multi-select__option"
          v-for="option in filteredOptions"
          :key="option.id"
          :class="{ 'multi-select__option--active': isSelected(option.id) }"
          @click="selectOption(option.id)"
        >
          <span>{{ option.name }}</span>
          <i class="fas fa-check" v-if="isSelected(option.id)"></i>
        </button>
        <div class="multi-select__empty" v-if="!filteredOptions.length">No matches found.</div>
      </div>
      <button type="button" class="multi-select__clear" v-if="multiple && value.length" @click="clearAll">
        Clear selected
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AgentSelectDropdown',
  props: {
    label: {
      type: String,
      required: true,
    },
    options: {
      type: Array,
      default: () => [],
    },
    value: {
      type: [Array, String, Number],
      default: null,
    },
    multiple: {
      type: Boolean,
      default: true,
    },
    // Ghost (pill-button) look matches Languages/Specialty/Near Me; boxed
    // matches the bordered, labelled City/Experience fields in the primary
    // search row.
    variant: {
      type: String,
      default: 'ghost',
    },
  },
  data() {
    return {
      open: false,
      query: '',
    };
  },
  computed: {
    filteredOptions() {
      if (!this.query) {
        return this.options;
      }

      const q = this.query.toLowerCase();

      return this.options.filter((option) => option.name.toLowerCase().includes(q));
    },
    toggleLabel() {
      if (this.multiple) {
        const count = (this.value || []).length;
        return count ? `${this.label} (${count})` : this.label;
      }

      const selected = this.options.find((option) => String(option.id) === String(this.value));
      return selected ? selected.name : this.label;
    },
  },
  mounted() {
    document.addEventListener('click', this.handleOutsideClick);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  methods: {
    toggle() {
      this.open = !this.open;

      if (this.open) {
        this.query = '';
      }
    },
    isSelected(id) {
      if (this.multiple) {
        return (this.value || []).includes(id);
      }

      return String(this.value) === String(id);
    },
    selectOption(id) {
      if (this.multiple) {
        const current = this.value || [];
        const next = current.includes(id) ? current.filter((v) => v !== id) : current.concat([id]);
        this.$emit('input', next);
        return;
      }

      this.$emit('input', id);
      this.open = false;
    },
    clearAll() {
      this.$emit('input', []);
    },
    handleOutsideClick(event) {
      if (this.open && this.$refs.root && !this.$refs.root.contains(event.target)) {
        this.open = false;
      }
    },
  },
};
</script>

<style scoped>
/*
  Self-contained on purpose: Vue 2's scoped CSS stamps each component's own
  template elements with that component's own data-v-* attribute, so
  AgentSearch.vue's own field/button rules (scoped to AgentSearch's hash)
  would never match elements rendered by this child component's template.
  Duplicating the small amount of look needed here avoids that mismatch.

  Per-instance width is driven by the --ms-grow/--ms-basis custom properties
  (set via an inline `style` attribute from the parent, which always applies
  regardless of scoping) so each usage can ask for a different share of its
  row without needing a dedicated CSS class per instance. The mobile media
  query below still wins over any inline custom property, since at equal
  selector specificity the later plain `flex` declaration in the cascade
  simply overrides it outright.
*/
.multi-select {
  position: relative;
  flex: var(--ms-grow, 1) 1 var(--ms-basis, 0%);
  min-width: 0;
}

.multi-select__field-label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--agent-label-gray, #9ca3af);
  margin-bottom: 4px;
}

.multi-select__toggle {
  width: 100%;
  height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  background: #f4f4f5;
  color: #1a1d24;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 14px;
  font-family: inherit;
  padding: 0 16px;
  cursor: pointer;
  transition: background 0.2s ease;
  white-space: nowrap;
}

.multi-select__toggle:hover {
  background: #eceef0;
}

/* ---- Boxed variant: matches the bordered, labelled primary-row fields ---- */
.multi-select--boxed {
  border: 1px solid var(--agent-border, #e5e7eb);
  border-radius: 8px;
  padding: 8px 16px;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.multi-select--boxed .multi-select__toggle {
  height: 24px;
  background: transparent;
  color: var(--agent-navy, #1a1d24);
  font-weight: 400;
  font-size: 14px;
  padding: 0;
}

.multi-select--boxed .multi-select__toggle:hover {
  background: transparent;
}

.multi-select--boxed .multi-select__panel {
  left: -17px;
}

.multi-select__toggle-label {
  overflow: hidden;
  text-overflow: ellipsis;
}

.multi-select__chevron {
  font-size: 11px;
  color: #9ca3af;
  transition: transform 0.15s ease;
  flex-shrink: 0;
}

.multi-select__chevron--open {
  transform: rotate(180deg);
}

.multi-select__panel {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  z-index: 20;
  width: 260px;
  max-width: 80vw;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 16px 40px rgba(26, 29, 36, 0.16);
  padding: 12px;
}

.multi-select__search {
  width: 100%;
  box-sizing: border-box;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 8px 10px;
  font-size: 13px;
  font-family: inherit;
  margin-bottom: 8px;
  outline: none;
}

.multi-select__search:focus {
  border-color: #e0a63e;
}

.multi-select__list {
  max-height: 220px;
  overflow-y: auto;
}

.multi-select__option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  width: 100%;
  background: none;
  border: none;
  text-align: left;
  font-size: 14px;
  font-family: inherit;
  color: #1a1d24;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
}

.multi-select__option:hover {
  background: #f6f2ea;
}

.multi-select__option--active {
  background: #f6f2ea;
  font-weight: 600;
}

.multi-select__option--active i {
  color: #e0a63e;
}

.multi-select__empty {
  font-size: 13px;
  color: #9ca3af;
  padding: 8px;
  text-align: center;
}

.multi-select__clear {
  width: 100%;
  margin-top: 8px;
  padding-top: 8px;
  border: none;
  border-top: 1px solid #e5e7eb;
  background: none;
  font-size: 12px;
  font-weight: 600;
  font-family: inherit;
  color: #6b7280;
  cursor: pointer;
}

.multi-select__clear:hover {
  color: #cf9530;
}

@media (max-width: 768px) {
  .multi-select {
    flex: 1 1 auto;
    width: 100%;
  }

  .multi-select__panel {
    width: 100%;
    max-width: none;
  }

  .multi-select--boxed .multi-select__panel {
    left: 0;
  }
}
</style>

<template>
  <div class="agent-search">
    <div class="agent-search__card">
      <div class="agent-search__row agent-search__row--primary">
        <div class="agent-search__keyword-field">
          <label class="agent-search__label">Search by name</label>
          <input
            type="text"
            class="agent-search__input"
            v-model="keyword"
            @keyup.enter="search"
            placeholder="e.g. Sarah Ahmed"
          />
        </div>
        <agent-select-dropdown
          label="City"
          :options="cityOptions"
          v-model="cityId"
          :multiple="false"
          variant="boxed"
          style="--ms-basis: 200px"
        />
        <div class="agent-search__field agent-search__field--boxed">
          <label class="agent-search__label">Experience</label>
          <select v-model="experienceBucket" class="agent-search__select">
            <option value="">All Experience</option>
            <option v-for="bucket in experienceBuckets" :key="bucket.value" :value="bucket.value">
              {{ bucket.label }}
            </option>
          </select>
        </div>
        <button type="button" class="agent-btn agent-btn--primary agent-search__submit" @click="search">
          <i class="fas fa-search"></i> Search
        </button>
      </div>

      <div class="agent-search__row agent-search__row--secondary">
        <agent-select-dropdown
          label="Country"
          :options="countryOptions"
          v-model="countryId"
          :multiple="false"
          style="--ms-grow: 1.5"
        />
        <div class="agent-search__field-divider"></div>

        <agent-select-dropdown label="Languages" :options="languageList" v-model="languageIds" />
        <agent-select-dropdown label="Specialty" :options="categoryList" v-model="categoryIds" />

        <button type="button" class="agent-btn agent-btn--outline agent-search__near-me" @click="requestNearMe" :disabled="locating">
          <i class="fas fa-location-arrow"></i> {{ locating ? 'Locating…' : 'Near Me' }}
        </button>
      </div>
    </div>

    <div class="agent-search__meta-row">
      <span class="agent-search__count">{{ meta.total }} agent{{ meta.total === 1 ? '' : 's' }} found</span>
      <div class="agent-search__pills">
        <button
          type="button"
          class="agent-pill"
          :class="{ 'agent-pill--active': categoryIds.length === 0 }"
          @click="selectPill(null)"
        >
          All Agents
        </button>
        <button
          type="button"
          class="agent-pill"
          v-for="category in topCategoryList"
          :key="category.id"
          :class="{ 'agent-pill--active': categoryIds.length === 1 && categoryIds[0] === category.id }"
          @click="selectPill(category.id)"
        >
          {{ category.name }}
        </button>
        <button type="button" class="agent-pill agent-pill--reset" @click="resetAll">Reset</button>
      </div>
    </div>

    <div class="agent-search__results">
      <div v-if="isLoading" class="agent-search__loading">
        <div class="half-circle-spinner">
          <div class="circle circle-1"></div>
          <div class="circle circle-2"></div>
        </div>
      </div>
      <div v-else-if="!data.length" class="agent-search__empty">No agents found matching your search.</div>
      <div v-else class="agent-search__grid">
        <figure class="agent-card" v-for="agent in data" :key="agent.id">
          <div class="agent-card__media">
            <img :src="agent.avatar" :alt="agent.name" class="agent-card__photo" />
            <span class="agent-card__verified"><i class="fas fa-check-circle"></i> Verified</span>
            <span class="agent-card__exp-badge" v-if="agent.years_of_experience">
              {{ agent.years_of_experience }} Years Experience
            </span>
          </div>
          <div class="agent-card__body">
            <div class="agent-card__top">
              <div class="agent-card__identity">
                <span class="agent-card__role" v-if="agent.specialties.length">{{ agent.specialties[0] }}</span>
                <span class="agent-card__role" v-else>Real Estate Agent</span>
                <figcaption class="agent-card__name">{{ agent.name }}</figcaption>
                <p class="agent-card__location" v-if="agent.city">
                  <i class="fas fa-map-marker-alt"></i> {{ agent.city }}<template v-if="agent.country">, {{ agent.country }}</template>
                </p>
              </div>
              <div class="agent-card__top-actions">
                <span class="agent-card__rating" v-if="agent.rating !== null">
                  <i class="fas fa-star"></i> {{ agent.rating }}
                </span>
                <div class="agent-card__action-wrap" v-if="agent.phone">
                  <a
                    class="agent-card__action"
                    :href="'tel:' + agent.phone"
                    @click="onPhoneClick($event, agent)"
                    title="Call agent"
                  >
                    <i class="fa fa-phone"></i>
                  </a>
                  <span v-if="!isMobile && phoneTooltipId === agent.id" class="agent-card__tooltip">{{ agent.phone }}</span>
                </div>
                <a
                  v-if="agent.email"
                  class="agent-card__action"
                  :href="'mailto:' + agent.email"
                  title="Email agent"
                >
                  <i class="fa fa-envelope"></i>
                </a>
              </div>
            </div>

            <div class="agent-card__tags" v-if="agent.specialties.length">
              <span class="agent-card__tag" v-for="specialty in agent.specialties.slice(0, 2)" :key="specialty">
                {{ specialty }}
              </span>
            </div>

            <p class="agent-card__meta" v-if="agent.description">{{ agent.description }}</p>
            <p class="agent-card__meta" v-if="agent.distance !== null">{{ agent.distance }} km away</p>
            <p class="agent-card__meta" v-if="agent.languages.length">{{ agent.languages.join(', ') }}</p>

            <div class="agent-card__footer">
              <div class="agent-card__stat">
                <strong>{{ agent.properties_count }}</strong>
                <span>{{ agent.properties_count === 1 ? 'Listing' : 'Listings' }}</span>
              </div>
              <a class="agent-card__view-profile" :href="'/agent-detail/' + agent.username">
                View Profile <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </figure>
      </div>

      <div class="agent-search__load-more" v-if="hasMore">
        <button type="button" class="agent-btn agent-btn--outline" @click="loadMore" :disabled="isLoadingMore">
          <template v-if="isLoadingMore">Loading…</template>
          <template v-else>Load More Agents <i class="fas fa-arrow-down"></i></template>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import AgentSelectDropdown from './AgentSelectDropdown.vue';

const EXPERIENCE_BUCKETS = {
  '1-5': [1, 5],
  '6-10': [6, 10],
  '11-15': [11, 15],
  '16-20': [16, 20],
  '21-25': [21, 25],
  '25+': [26, null],
};

export default {
  components: {
    AgentSelectDropdown,
  },
  props: {
    url: {
      type: String,
      required: true,
    },
    citiesUrl: {
      type: String,
      required: true,
    },
    countries: {
      type: String,
      default: '[]',
    },
    cities: {
      type: String,
      default: '[]',
    },
    languages: {
      type: String,
      default: '[]',
    },
    categories: {
      type: String,
      default: '[]',
    },
    topCategories: {
      type: String,
      default: '[]',
    },
    defaultCountryId: {
      type: [String, Number],
      default: '',
    },
    defaultCityId: {
      type: [String, Number],
      default: '',
    },
  },
  data() {
    return {
      keyword: '',
      countryId: this.defaultCountryId || '',
      cityId: this.defaultCityId || '',
      languageIds: [],
      categoryIds: [],
      experienceBucket: '',
      lat: null,
      lng: null,
      locating: false,
      isLoading: true,
      isLoadingMore: false,
      data: [],
      meta: {
        current_page: 1,
        last_page: 1,
        total: 0,
      },
      isMobile: false,
      phoneTooltipId: null,
      countryList: JSON.parse(this.countries),
      cityList: JSON.parse(this.cities),
      languageList: JSON.parse(this.languages),
      categoryList: JSON.parse(this.categories),
      topCategoryList: JSON.parse(this.topCategories),
      experienceBuckets: [
        { value: '1-5', label: '1-5 Years' },
        { value: '6-10', label: '6-10 Years' },
        { value: '11-15', label: '11-15 Years' },
        { value: '16-20', label: '16-20 Years' },
        { value: '21-25', label: '21-25 Years' },
        { value: '25+', label: '25+ Years' },
      ],
    };
  },
  mounted() {
    this.isMobile = /Android|iPhone|iPad|iPod|IEMobile|Opera Mini/i.test(navigator.userAgent);
    document.addEventListener('click', this.handleOutsideClick);
    this.fetchAgents();
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  computed: {
    hasMore() {
      return this.meta.current_page < this.meta.last_page;
    },
    countryOptions() {
      return [{ id: '', name: 'All Countries' }].concat(this.countryList);
    },
    cityOptions() {
      return [{ id: '', name: 'All Cities' }].concat(this.cityList);
    },
  },
  watch: {
    countryId() {
      this.onCountryChange();
    },
  },
  methods: {
    onCountryChange() {
      this.cityId = '';
      this.cityList = [];

      if (!this.countryId) {
        return;
      }

      axios.get(this.citiesUrl, { params: { country_id: this.countryId } }).then((response) => {
        this.cityList = response.data.data;
      });
    },
    requestNearMe() {
      if (!navigator.geolocation) {
        return;
      }

      this.locating = true;

      navigator.geolocation.getCurrentPosition(
        (position) => {
          this.lat = position.coords.latitude;
          this.lng = position.coords.longitude;
          this.locating = false;
          this.search();
        },
        () => {
          this.locating = false;
        }
      );
    },
    selectPill(categoryId) {
      if (categoryId === null) {
        this.categoryIds = [];
      } else if (this.categoryIds.length === 1 && this.categoryIds[0] === categoryId) {
        this.categoryIds = [];
      } else {
        this.categoryIds = [categoryId];
      }

      this.search();
    },
    resetAll() {
      this.keyword = '';
      this.countryId = this.defaultCountryId || '';
      this.cityId = this.defaultCityId || '';
      this.languageIds = [];
      this.categoryIds = [];
      this.experienceBucket = '';
      this.lat = null;
      this.lng = null;
      this.search();
    },
    search() {
      this.fetchAgents(1, false);
    },
    loadMore() {
      if (!this.hasMore || this.isLoadingMore) {
        return;
      }

      this.fetchAgents(this.meta.current_page + 1, true);
    },
    onPhoneClick(event, agent) {
      if (this.isMobile) {
        return;
      }

      event.preventDefault();
      this.phoneTooltipId = this.phoneTooltipId === agent.id ? null : agent.id;
    },
    handleOutsideClick(event) {
      if (this.phoneTooltipId !== null && !event.target.closest('.agent-card__action-wrap')) {
        this.phoneTooltipId = null;
      }
    },
    fetchAgents(page = 1, append = false) {
      if (append) {
        this.isLoadingMore = true;
      } else {
        this.isLoading = true;
      }

      const bucket = EXPERIENCE_BUCKETS[this.experienceBucket];

      axios
        .get(this.url, {
          params: {
            page,
            keyword: this.keyword || undefined,
            country_id: this.countryId || undefined,
            city_id: this.cityId || undefined,
            language_ids: this.languageIds.length ? this.languageIds : undefined,
            category_ids: this.categoryIds.length ? this.categoryIds : undefined,
            min_experience: bucket ? bucket[0] : undefined,
            max_experience: bucket && bucket[1] !== null ? bucket[1] : undefined,
            lat: this.lat || undefined,
            lng: this.lng || undefined,
          },
        })
        .then((response) => {
          // BaseHttpResponse wraps whatever the controller passes to
          // setData() under its own top-level 'data' key, so our
          // {data, meta} payload ends up nested one level deeper than a
          // typical Laravel Resource collection response.
          const payload = response.data.data;
          this.data = append ? this.data.concat(payload.data) : payload.data;
          this.meta = payload.meta;
          this.isLoading = false;
          this.isLoadingMore = false;
        });
    },
  },
};
</script>

<style scoped>
.agent-search {
  --agent-navy: #1a1d24;
  --agent-gold: #e0a63e;
  --agent-gold-hover: #cf9530;
  --agent-white: #ffffff;
  --agent-text-muted: #6b7280;
  --agent-label-gray: #9ca3af;
  --agent-border: #e5e7eb;
  --agent-card-bg: #f6f2ea;
  --agent-font-heading: 'Playfair Display', Georgia, serif;
  --agent-font-body: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

  font-family: var(--agent-font-body);
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 24px;
  position: relative;
}

/* ---- Search card ---- */
.agent-search__card {
  background: var(--agent-white);
  border-radius: 12px;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.16);
  padding: 24px;
  position: relative;
  z-index: 2;
}

.agent-search__row {
  display: flex;
  align-items: flex-end;
  gap: 16px;
  flex-wrap: wrap;
}

.agent-search__row--primary {
  align-items: stretch;
}

.agent-search__row--secondary {
  border-top: 1px solid var(--agent-border);
  margin-top: 16px;
  padding-top: 16px;
  align-items: center;
}

.agent-search__near-me {
  flex: 0.7;
  height: 40px;
}

.agent-search__keyword-field {
  flex: 0 1 300px;
}

.agent-search__field {
  flex: 1;
  min-width: 0;
  padding: 8px 0;
}

.agent-search__field--boxed {
  border: 1px solid var(--agent-border);
  border-radius: 8px;
  padding: 8px 16px;
  flex: 1 1 200px;
}

.agent-search__field-divider {
  width: 1px;
  align-self: stretch;
  background: var(--agent-border);
}

.agent-search__label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--agent-label-gray);
  margin-bottom: 4px;
}

.agent-search__input,
.agent-search__select {
  border: none;
  outline: none;
  font-size: 14px;
  color: var(--agent-navy);
  width: 100%;
  background: transparent;
  font-family: var(--agent-font-body);
  padding: 0;
  height: 24px;
}

.agent-search__keyword-field {
  border: 1px solid var(--agent-border);
  border-radius: 8px;
  padding: 8px 16px;
}

/* ---- Buttons ---- */
.agent-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 600;
  font-size: 14px;
  padding: 0 22px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
  white-space: nowrap;
}

.agent-btn:disabled {
  opacity: 0.6;
  cursor: default;
}

.agent-btn--primary {
  background: var(--agent-gold);
  color: var(--agent-white);
}

.agent-btn--primary:hover {
  background: var(--agent-gold-hover);
}

.agent-btn--outline {
  background: transparent;
  color: var(--agent-navy);
  border: 1px solid var(--agent-border);
}

.agent-btn--outline:hover {
  border-color: var(--agent-gold);
  color: var(--agent-gold-hover);
}

.agent-btn--ghost {
  background: #f4f4f5;
  color: var(--agent-navy);
}

.agent-btn--ghost:hover {
  background: #eceef0;
}

/* .agent-search__submit intentionally has no explicit height - the row's
   align-items: stretch (see .agent-search__row--primary) sizes it to match
   its labelled sibling fields automatically. */

/* ---- Agents-found / pill row ---- */
.agent-search__meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-top: 32px;
}

.agent-search__count {
  font-size: 14px;
  color: var(--agent-text-muted);
  flex-shrink: 0;
}

.agent-search__pills {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.agent-pill {
  background: #f4f4f5;
  color: var(--agent-navy);
  border: 1px solid transparent;
  border-radius: 999px;
  padding: 8px 18px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease;
  white-space: nowrap;
}

.agent-pill:hover {
  background: #eceef0;
}

.agent-pill--active {
  background: var(--agent-navy);
  color: var(--agent-white);
}

.agent-pill--reset {
  background: transparent;
  border-color: var(--agent-border);
  color: var(--agent-text-muted);
}

.agent-pill--reset:hover {
  border-color: var(--agent-gold);
  color: var(--agent-gold-hover);
}

/* ---- Results ---- */
.agent-search__results {
  padding: 24px 0 90px;
}

.agent-search__loading,
.agent-search__empty {
  text-align: center;
  padding: 60px 0;
  color: var(--agent-text-muted);
  font-size: 16px;
}

.agent-search__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.agent-card {
  background: var(--agent-card-bg);
  border-radius: 12px;
  overflow: hidden;
  margin: 0;
}

.agent-card__media {
  position: relative;
}

.agent-card__photo {
  display: block;
  width: 100%;
  height: 280px;
  object-fit: cover;
}

.agent-card__verified {
  position: absolute;
  top: 14px;
  left: 14px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: rgba(16, 24, 16, 0.55);
  backdrop-filter: blur(2px);
  color: #6fe08a;
  font-size: 11px;
  font-weight: 600;
  padding: 5px 10px;
  border-radius: 999px;
}

.agent-card__exp-badge {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.6), transparent);
  color: var(--agent-white);
  font-size: 12px;
  font-weight: 500;
  padding: 24px 14px 10px;
}

.agent-card__body {
  padding: 24px;
}

.agent-card__top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
}

.agent-card__identity {
  min-width: 0;
}

.agent-card__role {
  display: block;
  color: var(--agent-gold);
  font-weight: 600;
  font-size: 11px;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.agent-card__name {
  font-family: var(--agent-font-heading);
  font-weight: 700;
  font-size: 20px;
  color: var(--agent-navy);
  margin: 0 0 6px;
}

.agent-card__location {
  color: var(--agent-text-muted);
  font-size: 13px;
  margin: 0;
}

.agent-card__top-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.agent-card__rating {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  font-weight: 600;
  color: var(--agent-navy);
}

.agent-card__rating i {
  color: var(--agent-gold);
}

.agent-card__tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 12px;
}

.agent-card__tag {
  background: var(--agent-white);
  border: 1px solid var(--agent-border);
  color: var(--agent-navy);
  font-size: 11px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 999px;
}

.agent-card__meta {
  color: var(--agent-text-muted);
  font-size: 14px;
  margin: 10px 0 0;
}

.agent-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-top: 18px;
  padding-top: 16px;
  border-top: 1px solid var(--agent-border);
}

.agent-card__stat {
  display: flex;
  flex-direction: column;
}

.agent-card__stat strong {
  font-family: var(--agent-font-heading);
  font-size: 18px;
  color: var(--agent-navy);
  line-height: 1.2;
}

.agent-card__stat span {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--agent-label-gray);
}

.agent-card__action {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--agent-navy);
  color: var(--agent-white);
  font-size: 12px;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease;
}

.agent-card__action:hover {
  background: var(--agent-gold);
  color: var(--agent-navy);
}

.agent-card__action-wrap {
  position: relative;
  display: inline-flex;
}

.agent-card__tooltip {
  position: absolute;
  bottom: calc(100% + 8px);
  left: 0;
  background: var(--agent-navy);
  color: var(--agent-white);
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  padding: 6px 10px;
  border-radius: 6px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  z-index: 3;
}

.agent-card__tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 19px;
  transform: translateX(-50%);
  border: 5px solid transparent;
  border-top-color: var(--agent-navy);
}

.agent-card__view-profile {
  font-size: 13px;
  font-weight: 600;
  color: var(--agent-navy);
  text-decoration: none;
  border-bottom: 2px solid var(--agent-gold);
  padding-bottom: 2px;
  white-space: nowrap;
}

.agent-card__view-profile:hover {
  color: var(--agent-gold-hover);
}

/* ---- Load more ---- */
.agent-search__load-more {
  display: flex;
  justify-content: center;
  margin-top: 40px;
}

/* ---- Responsive ---- */
@media (max-width: 1200px) {
  .agent-search__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .agent-search {
    padding: 0 16px;
  }

  .agent-search__card {
    padding: 16px;
  }

  .agent-search__row {
    flex-direction: column;
    align-items: stretch;
  }

  .agent-search__keyword-field {
    flex: 1 1 auto;
  }

  .agent-search__field--boxed {
    flex: 1 1 auto;
  }

  .agent-search__field-divider {
    display: none;
  }

  .agent-search__submit,
  .agent-search__near-me {
    width: 100%;
  }

  .agent-search__meta-row {
    flex-direction: column;
    align-items: flex-start;
  }

  .agent-search__grid {
    grid-template-columns: 1fr;
  }
}
</style>

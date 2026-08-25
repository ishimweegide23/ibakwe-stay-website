// search-filter.js

// State management
let currentProperties = [...properties];
let displayedProperties = [];
let currentPage = 1;
const itemsPerPage = 6;
let isListView = localStorage.getItem('viewPref') === 'list';
let userCoords = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  mountSearchComponent();
  initSearchUI();
  initHistogram();
  loadSavedSearch();
  setupEventListeners();
  applyFilters(); // Initial render
});

function mountSearchComponent() {
  const grids = document.querySelectorAll('.properties-grid, .apartments-grid, .shortstay-grid, .houses-grid, .rental-grid, .sale-grid, .rent-grid, .sales-grid, .grid');
  if (grids.length > 0) {
    // Only target the first main grid to replace
    const grid = Array.from(grids).find(g => !g.closest('footer') && !g.closest('.header'));
    if (grid) {
      grid.id = 'properties-container';
      grid.innerHTML = ''; // clear static content
      
      const searchUI = document.createElement('div');
      searchUI.id = 'search-ui-container';
      grid.parentNode.insertBefore(searchUI, grid);
    }
  }
}

function initSearchUI() {
  const container = document.getElementById('search-ui-container');
  if (!container) return; // Not all pages might have the search UI

  container.innerHTML = `
    <div class="search-section">
      <div class="search-container">
        
        <div class="filter-chips">
          <div class="chip active" data-type="All"><i class="fas fa-home"></i> All</div>
          <div class="chip" data-type="Apartment"><i class="fas fa-building"></i> Apartments</div>
          <div class="chip" data-type="House"><i class="fas fa-house-user"></i> Houses</div>
          <div class="chip" data-type="Villa"><i class="fas fa-crown"></i> Villas</div>
          <div class="chip" data-type="Studio"><i class="fas fa-door-open"></i> Studios</div>
          <div class="chip" data-price="200000"><i class="fas fa-wallet"></i> Under 200k</div>
        </div>

        <div class="search-grid">
          <div class="search-group">
            <label>Location or Property Name</label>
            <div class="search-input-wrap">
              <i class="fas fa-search"></i>
              <input type="text" id="searchInput" placeholder="Search Nyarutarama..." autocomplete="off">
              <div id="autocompleteSuggestions" class="autocomplete-suggestions"></div>
            </div>
          </div>

          <div class="search-group">
            <label>Status</label>
            <div class="search-input-wrap">
              <i class="fas fa-tag"></i>
              <select id="statusFilter">
                <option value="All">All Statuses</option>
                <option value="For Rent">For Rent</option>
                <option value="For Sale">For Sale</option>
                <option value="Short Stay">Short Stay</option>
              </select>
            </div>
          </div>

          <div class="search-group">
            <label>Bedrooms</label>
            <div class="search-input-wrap">
              <i class="fas fa-bed"></i>
              <select id="bedsFilter">
                <option value="Any">Any</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
                <option value="4">4+</option>
              </select>
            </div>
          </div>

          <div class="search-group">
            <label>Bathrooms</label>
            <div class="search-input-wrap">
              <i class="fas fa-bath"></i>
              <select id="bathsFilter">
                <option value="Any">Any</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
              </select>
            </div>
          </div>

          <div class="price-filter-container">
            <div class="price-header">
              <label>Price Range (RWF)</label>
              <div class="price-display">
                <span id="priceMinDisplay">0</span> - <span id="priceMaxDisplay">1,000,000,000</span>+
              </div>
            </div>
            <div id="priceHistogram" class="price-histogram"></div>
            <div class="dual-slider">
              <div class="track" id="sliderTrack"></div>
              <input type="range" id="priceMin" min="0" max="1000000000" step="50000" value="0">
              <input type="range" id="priceMax" min="0" max="1000000000" step="50000" value="1000000000">
            </div>
          </div>
        </div>

        <div class="search-actions">
          <button class="btn-location" id="btnLocation">
            <i class="fas fa-location-arrow"></i> Use My Location
          </button>
          
          <div class="action-buttons">
            <button class="btn-reset" id="btnReset">
              <i class="fas fa-undo"></i> Reset
            </button>
            <button class="btn-save" id="btnSave">
              <i class="fas fa-heart"></i> Save Search
            </button>
            <button class="btn-search" id="btnSearch">
              <i class="fas fa-search"></i> Search Properties
            </button>
          </div>
        </div>
      </div>
    </div>
  `;

  // Inject Results Controls before properties grid
  const propertiesContainer = document.getElementById('properties-container');
  if (propertiesContainer) {
    const controlsDiv = document.createElement('div');
    controlsDiv.className = 'container';
    controlsDiv.innerHTML = `
      <div class="results-controls">
        <div class="results-count" id="resultsCount">Showing 0 properties</div>
        <div class="view-sort-controls">
          <select id="sortSelect" class="sort-select">
            <option value="default">Sort By: Recommended</option>
            <option value="price-asc">Price: Low to High</option>
            <option value="price-desc">Price: High to Low</option>
            <option value="newest">Newest First</option>
            <option value="size">Size: Largest First</option>
          </select>
          <div class="view-toggles">
            <button class="view-btn ${!isListView ? 'active' : ''}" id="btnGridView" title="Grid View"><i class="fas fa-th"></i></button>
            <button class="view-btn ${isListView ? 'active' : ''}" id="btnListView" title="List View"><i class="fas fa-list"></i></button>
          </div>
        </div>
      </div>
    `;
    propertiesContainer.parentNode.insertBefore(controlsDiv, propertiesContainer);
  }
}

function initHistogram() {
  const histo = document.getElementById('priceHistogram');
  if (!histo) return;
  // Generate mock histogram bars
  for(let i=0; i<30; i++) {
    const bar = document.createElement('div');
    bar.className = 'histogram-bar active';
    const height = Math.floor(Math.random() * 30) + 10;
    bar.style.height = height + 'px';
    histo.appendChild(bar);
  }
}

function setupEventListeners() {
  // Input tracking
  const minSlider = document.getElementById('priceMin');
  const maxSlider = document.getElementById('priceMax');
  
  if (minSlider && maxSlider) {
    const updateSliders = () => {
      let min = parseInt(minSlider.value);
      let max = parseInt(maxSlider.value);
      if (min > max - 50000) {
        if (event.target === minSlider) minSlider.value = max - 50000;
        else maxSlider.value = min + 50000;
        min = parseInt(minSlider.value);
        max = parseInt(maxSlider.value);
      }
      
      document.getElementById('priceMinDisplay').innerText = min.toLocaleString();
      document.getElementById('priceMaxDisplay').innerText = max.toLocaleString();
      
      const percent1 = (min / maxSlider.max) * 100;
      const percent2 = (max / maxSlider.max) * 100;
      document.getElementById('sliderTrack').style.left = percent1 + '%';
      document.getElementById('sliderTrack').style.width = (percent2 - percent1) + '%';
    };
    
    minSlider.addEventListener('input', updateSliders);
    maxSlider.addEventListener('input', updateSliders);
  }

  // Chips
  document.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', (e) => {
      document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
      e.currentTarget.classList.add('active');
      applyFilters();
    });
  });

  // Buttons
  const btnSearch = document.getElementById('btnSearch');
  if (btnSearch) btnSearch.addEventListener('click', applyFilters);
  
  const btnReset = document.getElementById('btnReset');
  if (btnReset) btnReset.addEventListener('click', resetFilters);

  const btnSave = document.getElementById('btnSave');
  if (btnSave) btnSave.addEventListener('click', saveSearch);

  const btnLoc = document.getElementById('btnLocation');
  if (btnLoc) btnLoc.addEventListener('click', requestLocation);

  // View toggles
  const gridBtn = document.getElementById('btnGridView');
  const listBtn = document.getElementById('btnListView');
  
  if (gridBtn && listBtn) {
    gridBtn.addEventListener('click', () => setViewMode('grid'));
    listBtn.addEventListener('click', () => setViewMode('list'));
  }

  // Sort select
  const sortSel = document.getElementById('sortSelect');
  if (sortSel) sortSel.addEventListener('change', () => {
    sortProperties();
    renderPage(1);
  });

  // Autocomplete
  const searchInput = document.getElementById('searchInput');
  const suggestionsBox = document.getElementById('autocompleteSuggestions');
  
  if (searchInput && suggestionsBox) {
    searchInput.addEventListener('input', (e) => {
      const val = e.target.value.toLowerCase();
      suggestionsBox.innerHTML = '';
      if (!val) {
        suggestionsBox.style.display = 'none';
        return;
      }
      
      const matches = rwandaLocations.filter(loc => loc.toLowerCase().includes(val));
      if (matches.length > 0) {
        matches.forEach(match => {
          const div = document.createElement('div');
          div.className = 'suggestion-item';
          div.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${match}`;
          div.addEventListener('click', () => {
            searchInput.value = match;
            suggestionsBox.style.display = 'none';
            applyFilters();
          });
          suggestionsBox.appendChild(div);
        });
      } else {
        suggestionsBox.innerHTML = `<div class="suggestion-item" style="color:var(--gray)">No locations found. Use own knowledge.</div>`;
      }
      suggestionsBox.style.display = 'block';
    });

    document.addEventListener('click', (e) => {
      if (e.target !== searchInput) suggestionsBox.style.display = 'none';
    });
  }

  // Read URL params initially
  readUrlParams();
}

function applyFilters() {
  showSkeletons();
  
  setTimeout(() => {
    const textQuery = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const status = document.getElementById('statusFilter')?.value || 'All';
    const beds = document.getElementById('bedsFilter')?.value || 'Any';
    const baths = document.getElementById('bathsFilter')?.value || 'Any';
    const minPrice = document.getElementById('priceMin') ? parseInt(document.getElementById('priceMin').value) : 0;
    const maxPrice = document.getElementById('priceMax') ? parseInt(document.getElementById('priceMax').value) : 1000000000;
    
    const activeChip = document.querySelector('.chip.active');
    const chipType = activeChip ? activeChip.getAttribute('data-type') : 'All';
    const chipPrice = activeChip ? activeChip.getAttribute('data-price') : null;

    currentProperties = properties.filter(p => {
      // Text Search
      if (textQuery && !p.title.toLowerCase().includes(textQuery) && !p.location.toLowerCase().includes(textQuery) && !p.description.toLowerCase().includes(textQuery)) {
        return false;
      }
      
      // Status
      if (status !== 'All' && p.status !== status) return false;
      
      // Beds/Baths
      if (beds !== 'Any' && p.bedrooms < parseInt(beds)) return false;
      if (baths !== 'Any' && p.bathrooms < parseInt(baths)) return false;
      
      // Price Slider
      if (p.price < minPrice || p.price > maxPrice) return false;

      // Chip Overrides
      if (chipType && chipType !== 'All' && p.type !== chipType) return false;
      if (chipPrice && p.price > parseInt(chipPrice)) return false;

      return true;
    });

    // Update Analytics (mock)
    if (textQuery) {
      const searches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
      if(!searches.includes(textQuery)) {
        searches.push(textQuery);
        localStorage.setItem('recentSearches', JSON.stringify(searches.slice(-5)));
      }
    }

    updateUrlParams();
    sortProperties();
    currentPage = 1;
    renderPage(1);
    
  }, 400); // Fake network delay for smooth transition
}

function sortProperties() {
  const sort = document.getElementById('sortSelect')?.value || 'default';
  
  if (sort === 'price-asc') {
    currentProperties.sort((a,b) => a.price - b.price);
  } else if (sort === 'price-desc') {
    currentProperties.sort((a,b) => b.price - a.price);
  } else if (sort === 'newest') {
    currentProperties.sort((a,b) => new Date(b.dateAdded) - new Date(a.dateAdded));
  } else if (sort === 'size') {
    currentProperties.sort((a,b) => b.size - a.size);
  } else if (userCoords) {
    // Sort by distance if location known
    currentProperties.sort((a,b) => {
      return getDistance(userCoords.lat, userCoords.lng, a.coordinates.lat, a.coordinates.lng) - 
             getDistance(userCoords.lat, userCoords.lng, b.coordinates.lat, b.coordinates.lng);
    });
  } else {
    // Default: featured first, then trending
    currentProperties.sort((a,b) => {
      if(a.isFeatured && !b.isFeatured) return -1;
      if(!a.isFeatured && b.isFeatured) return 1;
      return b.views - a.views;
    });
  }
}

function renderPage(page) {
  const container = document.getElementById('properties-container');
  if (!container) return;

  const countDisplay = document.getElementById('resultsCount');
  if (countDisplay) {
    countDisplay.innerText = `Showing ${currentProperties.length} properties`;
  }

  if (currentProperties.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <i class="fas fa-search-minus"></i>
        <h3>No Properties Found</h3>
        <p>Try adjusting your filters or use your own knowledge to search different locations.</p>
        <button class="btn btn-primary" onclick="resetFilters()" style="margin-top: 15px;">Clear Filters</button>
      </div>
    `;
    removeLoadMore();
    return;
  }

  const start = 0;
  const end = page * itemsPerPage;
  displayedProperties = currentProperties.slice(start, end);

  container.innerHTML = '';
  container.className = isListView ? 'properties-grid list-view' : 'properties-grid';

  displayedProperties.forEach(p => {
    const card = document.createElement('div');
    card.className = 'property-card';
    card.style.opacity = '0'; // For animation
    
    // Badges
    let badgesHTML = '<div class="badges">';
    if (p.isFeatured) badgesHTML += '<span class="badge badge-featured">Featured</span>';
    const daysOld = (new Date() - new Date(p.dateAdded)) / (1000 * 60 * 60 * 24);
    if (daysOld < 30) badgesHTML += '<span class="badge badge-new">New</span>';
    if (p.views > 2000) badgesHTML += '<span class="badge badge-trending"><i class="fas fa-fire"></i> Trending</span>';
    badgesHTML += '</div>';

    // Distance
    let distanceHTML = '';
    if (userCoords) {
      const dist = getDistance(userCoords.lat, userCoords.lng, p.coordinates.lat, p.coordinates.lng).toFixed(1);
      distanceHTML = `<div class="distance-indicator"><i class="fas fa-location-arrow"></i> ${dist} km</div>`;
    }

    card.innerHTML = `
      <div class="property-img">
        ${badgesHTML}
        ${distanceHTML}
        <img src="${p.image}" alt="${p.title}">
      </div>
      <div class="property-content">
        <h3>${p.title}</h3>
        <div class="property-location">
          <i class="fas fa-map-marker-alt"></i>
          <span>${p.location}, Kigali</span>
        </div>
        <div class="property-features">
          <div class="feature"><i class="fas fa-bed"></i> <span>${p.bedrooms} Beds</span></div>
          <div class="feature"><i class="fas fa-bath"></i> <span>${p.bathrooms} Baths</span></div>
          <div class="feature"><i class="fas fa-vector-square"></i> <span>${p.size} m²</span></div>
        </div>
        <div class="property-price">
          ${p.priceLabel}
        </div>
        <a href="apartment.html" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 15px;">View Details</a>
      </div>
    `;
    container.appendChild(card);
    
    // Fade in
    setTimeout(() => { card.style.opacity = '1'; }, 50);
  });

  if (currentProperties.length > end) {
    addLoadMore();
  } else {
    removeLoadMore();
  }
}

function showSkeletons() {
  const container = document.getElementById('properties-container');
  if (!container) return;
  container.className = isListView ? 'properties-grid list-view' : 'properties-grid';
  container.innerHTML = '';
  
  for(let i=0; i<3; i++) {
    container.innerHTML += `
      <div class="skeleton-card">
        <div class="skeleton skeleton-img"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text short"></div>
      </div>
    `;
  }
}

function addLoadMore() {
  removeLoadMore(); // clear existing
  const container = document.getElementById('properties-container');
  if (!container) return;
  
  const div = document.createElement('div');
  div.className = 'pagination-container';
  div.id = 'loadMoreContainer';
  div.innerHTML = `<button class="btn-load-more" onclick="loadMore()">Load More Properties</button>`;
  container.parentNode.insertBefore(div, container.nextSibling);
}

function removeLoadMore() {
  const existing = document.getElementById('loadMoreContainer');
  if (existing) existing.remove();
}

function loadMore() {
  currentPage++;
  renderPage(currentPage);
}

function resetFilters() {
  if(document.getElementById('searchInput')) document.getElementById('searchInput').value = '';
  if(document.getElementById('statusFilter')) document.getElementById('statusFilter').value = 'All';
  if(document.getElementById('bedsFilter')) document.getElementById('bedsFilter').value = 'Any';
  if(document.getElementById('bathsFilter')) document.getElementById('bathsFilter').value = 'Any';
  
  if(document.getElementById('priceMin')) {
    document.getElementById('priceMin').value = '0';
    document.getElementById('priceMax').value = '1000000000';
    document.getElementById('priceMin').dispatchEvent(new Event('input'));
  }
  
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  document.querySelector('.chip[data-type="All"]')?.classList.add('active');
  
  applyFilters();
}

function setViewMode(mode) {
  isListView = (mode === 'list');
  localStorage.setItem('viewPref', mode);
  
  const gridBtn = document.getElementById('btnGridView');
  const listBtn = document.getElementById('btnListView');
  
  if (gridBtn && listBtn) {
    if (isListView) {
      gridBtn.classList.remove('active');
      listBtn.classList.add('active');
    } else {
      listBtn.classList.remove('active');
      gridBtn.classList.add('active');
    }
  }
  
  renderPage(currentPage); // Re-render without full filter
}

function requestLocation() {
  const btn = document.getElementById('btnLocation');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Locating...';
  
  if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition((pos) => {
      userCoords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
      btn.innerHTML = '<i class="fas fa-check"></i> Location Used';
      btn.style.backgroundColor = 'var(--primary)';
      btn.style.color = 'white';
      applyFilters();
    }, () => {
      // Mock coordinates for Kigali Center if denied/failed
      userCoords = { lat: -1.9441, lng: 30.0619 };
      btn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Using Kigali Center';
      applyFilters();
    });
  } else {
    btn.innerHTML = '<i class="fas fa-times"></i> Not Supported';
  }
}

// Haversine formula
function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // km
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  return R * c;
}

function saveSearch() {
  const searchObj = {
    text: document.getElementById('searchInput')?.value,
    status: document.getElementById('statusFilter')?.value,
    date: new Date().toLocaleDateString()
  };
  localStorage.setItem('savedSearch', JSON.stringify(searchObj));
  
  const btn = document.getElementById('btnSave');
  const oldHTML = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-check"></i> Saved!';
  btn.style.backgroundColor = 'var(--secondary)';
  btn.style.color = 'white';
  
  setTimeout(() => {
    btn.innerHTML = oldHTML;
    btn.style.backgroundColor = '';
    btn.style.color = '';
  }, 2000);
}

function loadSavedSearch() {
  const saved = localStorage.getItem('savedSearch');
  if (saved) {
    // We could offer a prompt here, but URL params take precedence
  }
}

function updateUrlParams() {
  const url = new URL(window.location);
  const q = document.getElementById('searchInput')?.value;
  if(q) url.searchParams.set('q', q);
  else url.searchParams.delete('q');
  
  window.history.pushState({}, '', url);
}

function readUrlParams() {
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q');
  if (q && document.getElementById('searchInput')) {
    document.getElementById('searchInput').value = q;
  }
}

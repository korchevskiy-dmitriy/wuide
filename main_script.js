const modal = document.getElementById('auth-modal-id');
const signInBtn = document.getElementById('reg_signin_id');
const logInBtn = document.getElementById('reg_login_id');
const closeBtn = document.getElementById('icon_cross_id');

const modalTitle = document.getElementById('modal-title');
const signInForm = document.getElementById('signin-form');
const logInForm = document.getElementById('login-form');

function openModal() {
    modal.classList.add('open');
    document.body.classList.add('no-scroll'); 
}

function closeModal() {
    modal.classList.remove('open');
    document.body.classList.remove('no-scroll');
}

signInBtn.addEventListener('click', () => {
    openModal();
    logInForm.classList.add('hidden');
    signInForm.classList.remove('hidden');
    modalTitle.textContent = 'Sign in';
});

closeBtn.addEventListener('click', () => {
    closeModal();
});

logInBtn.addEventListener('click', () => {
    openModal(); 
    signInForm.classList.add('hidden');
    logInForm.classList.remove('hidden');
    modalTitle.textContent = 'Log in';
});

window.addEventListener('click', (event) => {
    if (event.target === modal){
        closeModal(); 
    }
});

const signInEndBtn = document.getElementById('reg_signin_footer_id');
const logInEndBtn = document.getElementById('reg_login_footer_id');

signInEndBtn.addEventListener('click', () => {
    openModal();
    logInForm.classList.add('hidden');
    signInForm.classList.remove('hidden');
    modalTitle.textContent = 'Sign in';    
});

logInEndBtn.addEventListener('click', () => {
    openModal();
    signInForm.classList.add('hidden');
    logInForm.classList.remove('hidden');
    modalTitle.textContent = 'Log in';
});
const exploreNowBtn = document.getElementById('exp_id');
const firstSection = document.getElementById('first_section_id');

exploreNowBtn.addEventListener('click', () => {
    firstSection.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
});






const btnPrev = document.getElementById('btn-prev');
const btnNext = document.getElementById('btn-next');
const tracks = document.querySelectorAll('.slider-track');

const itemsVisible = 4;
const originalTotalItems = tracks[0].children.length; 

const itemWidth = 25; 

tracks.forEach(track => {
    for (let i = 0; i < itemsVisible + 3; i++) {
        const clone = track.children[i].cloneNode(true);
        track.appendChild(clone);
    }

    track.style.transition = 'transform 0.5s ease-in-out';
});

let currentSlideIndex = 0;
let isAnimating = false;

function updateSlider(withAnimation = true) {
    const offset = -(currentSlideIndex * itemWidth);
    
    tracks.forEach(track => {
        track.style.transition = withAnimation ? 'transform 0.5s ease-in-out' : 'none';
        track.style.transform = `translateX(${offset}%)`;
    });
}

btnNext.addEventListener('click', () => {
    if (isAnimating) return; 
    isAnimating = true;

    currentSlideIndex++;
    updateSlider(true);

    if (currentSlideIndex === originalTotalItems) {
        setTimeout(() => {
            currentSlideIndex = 0; 
            updateSlider(false);  
            isAnimating = false;   
        }, 500);
    } else {
        setTimeout(() => {
            isAnimating = false;
        }, 500);
    }
});

btnPrev.addEventListener('click', () => {
    if (isAnimating) return;
    
    if (currentSlideIndex > 0) {
        isAnimating = true;
        currentSlideIndex--;
        updateSlider(true);
        setTimeout(() => { isAnimating = false; }, 500);
    } 
    else {
        isAnimating = true;
        currentSlideIndex = originalTotalItems - 1;
        updateSlider(true);
        setTimeout(() => { isAnimating = false; }, 500);
    }
});


const passToggleBtn = document.querySelectorAll('.password-toggle');
passToggleBtn.forEach((toggle) =>{
    toggle.addEventListener('click', () => {
        const passInputType = toggle.previousElementSibling
        if (passInputType.type === 'password'){
            passInputType.type = 'text';
            toggle.textContent = '🙈';
        }
        else{
            passInputType.type = 'password';
            toggle.textContent = '👁';
        }
    });
});


const fileInput = document.getElementById('user_photo_form');
const photoConteiner = document.getElementById('user_photo_id');

fileInput.addEventListener('change', function()  {
    const file = this.files[0];
    if (file){
        const reader = new FileReader();
        reader.onload = function(event){
            const url = event.target.result;
            photoConteiner.style.backgroundImage = `url(${url})`;
            photoConteiner.classList.add('has-image');
        }
        reader.readAsDataURL(file);
    }
});


const hiddenElements = document.querySelectorAll('.hidden-element');


const observer = new IntersectionObserver((entries) => {

    entries.forEach((entry) => {

        if (entry.isIntersecting) {
            
            entry.target.classList.add('show');
            observer.unobserve(entry.target);
        }
    });
});

hiddenElements.forEach((el) => observer.observe(el));



/* phone scrolling */
const galleryContainer = document.querySelector('.gallery');
const sliderTracks = document.querySelectorAll('.slider-track');

function setTrackSpeed(speed) {
    sliderTracks.forEach(track => {
        const animations = track.getAnimations();
        animations.forEach(anim => {
            if (anim.updatePlaybackRate) {
                anim.updatePlaybackRate(speed);
            } else {
                anim.playbackRate = speed;
            }
        });
    });
}

if (galleryContainer && sliderTracks.length > 0) {
    
    setTimeout(() => {
        setTrackSpeed(0.33); 
    }, 100);

    galleryContainer.addEventListener('touchstart', () => {
        setTrackSpeed(1.0); 
    });

    galleryContainer.addEventListener('touchend', () => {
        setTrackSpeed(0.33);
    });

    galleryContainer.addEventListener('mousedown', () => {
        setTrackSpeed(1.0);
    });

    galleryContainer.addEventListener('mouseup', () => {
        setTrackSpeed(0.33);
    });
    
    galleryContainer.addEventListener('mouseleave', () => {
        setTrackSpeed(0.33);
    });
}
/* smart filters */
let allCountriesData = [];
let isExpanded = false; 

const filterPriceBtn = document.getElementById('filters_price_id');
const filterPriceList = document.getElementById('price-list-id');
const priceArrow = document.getElementById('arrow_icon_price_id');
const priceBtnText = filterPriceBtn.querySelector('span');
const priceOptions = document.querySelectorAll('#price-list-id .option-btn');

const filterRegionBtn = document.getElementById('filters_region_id');
const filterRegionList = document.getElementById('region-list-id');
const regionArrow = document.getElementById('arrow_icon_region_id');
const regionBtnText = filterRegionBtn.querySelector('span');
const regionOptions = document.querySelectorAll('#region-list-id .option-btn');

const showMoreBtn = document.getElementById('show_more_id');

let currentPriceFilter = null;
let currentRegionFilter = null;


filterPriceBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    filterPriceList.hidden = !filterPriceList.hidden;
    priceArrow.classList.toggle('rotate');
    filterRegionList.hidden = true;
});

filterRegionBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    filterRegionList.hidden = !filterRegionList.hidden;
    regionArrow.classList.toggle('rotate');
    filterPriceList.hidden = true;
});

window.addEventListener('click', () => {
    filterPriceList.hidden = true;
    priceArrow.classList.remove('rotate');
    filterRegionList.hidden = true;
    regionArrow.classList.remove('rotate');
});


priceOptions.forEach((option) => {
    option.addEventListener('click', () => {
        const selectedPrice = option.textContent.trim();
        priceBtnText.textContent = selectedPrice;
        currentPriceFilter = selectedPrice;

        isExpanded = false;

        filterPriceList.hidden = true;
        priceArrow.classList.remove('rotate');

        applyFilters(); 
    });
});


regionOptions.forEach((option) => {
    option.addEventListener('click', () => {
        const selectedRegion = option.textContent.trim();
        regionBtnText.textContent = selectedRegion;
        currentRegionFilter = selectedRegion;

        isExpanded = false;

        filterRegionList.hidden = true;
        regionArrow.classList.remove('rotate');

        applyFilters(); 
    });
});

if (showMoreBtn) {
    showMoreBtn.addEventListener('click', (e) => {
        e.preventDefault(); 
        
        isExpanded = true;
        
        applyFilters();
    });
}


function applyFilters() {
    console.log("Фильтр -> Регион:", currentRegionFilter, "| Цена:", currentPriceFilter);

    const filtered = allCountriesData.filter(country => {
        if (currentPriceFilter && country.price !== currentPriceFilter) {
            return false;
        }
        if (currentRegionFilter && country.region.toLowerCase() !== currentRegionFilter.toLowerCase()) {
            return false;
        }
        return true; 
    });

    renderCountries(filtered);
}


function renderCountries(countriesList) {
    const container = document.getElementById('country_cards_block_id');
    if (!container) return;

    container.innerHTML = ''; 

    if (countriesList.length === 0) {
        container.innerHTML = '<h3 style="color: white; width: 100%; text-align: center; margin-top: 50px;">No countries found :(</h3>';
        if(showMoreBtn) showMoreBtn.style.display = 'none'; 
        return;
    }

    let visibleItems = countriesList;

    if (!isExpanded && countriesList.length > 3) {
        visibleItems = countriesList.slice(0, 3);
        
        if(showMoreBtn) showMoreBtn.style.display = 'flex'; 
    } else {
        if(showMoreBtn) showMoreBtn.style.display = 'none';
    }

    visibleItems.forEach(country => {
        const cardHTML = `
            <div class="country_card hidden-element show">
                <div class="country_img">
                    <img class="main_photo" src="${country.country_photo}" alt="${country.country}">
                    
                    <a href="country.html?id=${country.id}" class="arrow_more">
                        <img class="arrow-card" src="img_main/Frame 20.svg" alt="More">
                    </a>
                </div>
                <div class="description_country">
                    <div class="name_and_flag">
                        <h4 class="name_of_country">${country.country}</h4>
                        <img src="${country.flag_url}" class="flag_of_country" alt="${country.country} flag">
                    </div>
                    <div class="exactly_price">
                        <span class="text_price">Price</span>
                        <span class="count">${country.price}</span> 
                    </div>
                    <p class="description_of_country">
                        ${country.short_description}
                    </p>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHTML);
    });
}


async function loadCountries() {
    console.log("Loading countries...");
    try {
        const response = await fetch('http://localhost:8888/wuide/api.php/countries');
        allCountriesData = await response.json();
        
        applyFilters(); 
    } catch (error) {
        console.error("Ошибка:", error);
    }
}

loadCountries();
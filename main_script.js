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

const filterPriceBtn = document.getElementById('filters_price_id');
const filterPriceList = document.getElementById('price-list-id');
const priceArrow = document.getElementById('arrow_icon_price_id');

filterPriceBtn.addEventListener('click', () => {
    filterPriceList.hidden = !filterPriceList.hidden;
    priceArrow.classList.toggle('rotate');
});

const filterRegionBtn = document.getElementById('filters_region_id');
const filterRegionList = document.getElementById('region-list-id');
const regionArrow = document.getElementById('arrow_icon_region_id');

filterRegionBtn.addEventListener('click', () => {
    filterRegionList.hidden = !filterRegionList.hidden;
    regionArrow.classList.toggle('rotate');
});

const priceOptions = document.querySelectorAll('#price-list-id .option-btn');
const priceBtnText = filterPriceBtn.querySelector('span');

priceOptions.forEach((option) => {
    option.addEventListener('click', () => {
        priceBtnText.textContent = option.textContent;
        filterPriceList.hidden = true;
        priceArrow.classList.toggle('rotate');
    })
});

const regionOptions = document.querySelectorAll('#region-list-id .option-btn');
const regionBtnText = filterRegionBtn.querySelector('span');

regionOptions.forEach((option) => {
    option.addEventListener('click', () => {
        regionBtnText.textContent = option.textContent;
        filterRegionList.hidden = true;
        regionArrow.classList.toggle('rotate');
    })
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
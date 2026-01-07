
const modal = document.getElementById('auth-modal-id');
const signInBtn = document.getElementById('reg_signin_id');
const logInBtn = document.getElementById('reg_login_id');
const closeBtn = document.getElementById('icon_cross_id');

const modalTitle = document.getElementById('modal-title');
const signInForm = document.getElementById('signin-form');
const logInForm = document.getElementById('login-form');

signInBtn.addEventListener('click', () => {
    modal.classList.add('open');
    logInForm.classList.add('hidden');
    signInForm.classList.remove('hidden');
    modalTitle.textContent = 'Sign in';
});

const backBtn = document.getElementById('come-back-btn');

if (backBtn) {
    backBtn.addEventListener('click', () => {
        window.location.href = 'Wuide.html';
    });
}

closeBtn.addEventListener('click', () => {
    modal.classList.remove('open');
});

logInBtn.addEventListener('click', () => {
    modal.classList.add('open');
    signInForm.classList.add('hidden');
    logInForm.classList.remove('hidden');
    modalTitle.textContent = 'Log in';
});

window.addEventListener('click', (event) => {
    if (event.target === modal){
        modal.classList.remove('open');
    }
});

const signInEndBtn = document.getElementById('reg_signin_footer_id');
const logInEndBtn = document.getElementById('reg_login_footer_id');

signInEndBtn.addEventListener('click', () => {
    modal.classList.add('open');
    logInForm.classList.add('hidden');
    signInForm.classList.remove('hidden');
    modalTitle.textContent = 'Sign in';    
});

logInEndBtn.addEventListener('click', () => {
    modal.classList.add('open');
    signInForm.classList.add('hidden');
    logInForm.classList.remove('hidden');
    modalTitle.textContent = 'Log in';
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
        setTrackSpeed(1.0); 
    }, 100);

    galleryContainer.addEventListener('touchstart', () => {
        setTrackSpeed(3.0); 
    });

    galleryContainer.addEventListener('touchend', () => {
        setTrackSpeed(1.0);
    });

    galleryContainer.addEventListener('mousedown', () => {
        setTrackSpeed(3.0);
    });

    galleryContainer.addEventListener('mouseup', () => {
        setTrackSpeed(1.0);
    });
    
    galleryContainer.addEventListener('mouseleave', () => {
        setTrackSpeed(3.0);
    });
}








/* downloading data of country */

document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);
    const countryId = params.get('id');

    if (!countryId) return;

    console.log("Загружаем данные для страны ID:", countryId);

    try {
        const response = await fetch(`http://localhost:8888/wuide/api.php/countries/${countryId}`);
        
        if (!response.ok) {
            console.error("Страна не найдена");
            return;
        }

        const country = await response.json();
        console.log("Данные с сервера:", country);

        
        const title = document.getElementById('country-name');
        if (title) title.textContent = country.country;

        
        const shortDesc = document.getElementById('country-short-desc');
        if (shortDesc) shortDesc.textContent = country.short_description;

        
        const fullDesc = document.getElementById('country-full-desc');
        if (fullDesc) fullDesc.textContent = country.full_description;

        const capital = document.getElementById('info-capital');
        if (capital) capital.textContent = country.capital;

        const duration = document.getElementById('info-duration');
        if (duration) duration.textContent = country.visit_duration;

        const time = document.getElementById('info-time');
        if (time) time.textContent = country.visiting_time;

        const priceBlock = document.getElementById('price-range');
        if (priceBlock) priceBlock.textContent = ``;


        const placesContainer = document.getElementById('places-container');
        if (placesContainer && country.places.length > 0) {
            placesContainer.innerHTML = '';
            
            country.places.forEach(place => {
                const html = `
                    <div class="place-card">
                        <img class="place-photos" src="${place.photo_url}" alt="${place.name}">
                        <p class="place-title">${place.name}</p>
                    </div>
                `;
                placesContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        const foodContainer = document.getElementById('food-container');
        if (foodContainer && country.foods.length > 0) {
            foodContainer.innerHTML = ''; 
            
            country.foods.forEach(food => {
                const html = `
                    <div class="place-card">
                        <img class="food-photos" src="${food.photo_url}" alt="${food.name}">
                        <p class="place-title">${food.name}</p>
                    </div>
                `;
                foodContainer.insertAdjacentHTML('beforeend', html);
            });
        }

    } catch (error) {
        console.error("Ошибка загрузки:", error);
    }
});
/* modal logic */

document.addEventListener('DOMContentLoaded', () => {
    initReviewsModal();
});

function initReviewsModal() {
    const params = new URLSearchParams(window.location.search);
    const countryId = params.get('id');

    const modal = document.getElementById('reviews-modal');
    const openBtn = document.getElementById('open-reviews-modal-btn');
    const closeBtn = document.getElementById('close-reviews-modal');
    
    const reviewsList = document.getElementById('reviews-list-container');
    const guestMsg = document.getElementById('review-guest-msg');
    const userForm = document.getElementById('review-user-form');
    const textArea = document.getElementById('review-text-input');
    const postBtn = document.getElementById('post-review-btn');
    const loginLink = document.getElementById('trigger-login-from-modal');

    const modalImg = document.getElementById('modal-country-img');
    const modalName = document.getElementById('modal-country-name');
    const modalDesc = document.getElementById('modal-country-desc');

    if (!openBtn || !modal) return;

    openBtn.addEventListener('click', async () => {
        modal.classList.add('open');
        document.body.classList.add('no-scroll');
        
        updateModalHeader();
        checkAuthDisplay();
        await loadReviews(countryId);
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    function closeModal() {
        modal.classList.remove('open');
        document.body.classList.remove('no-scroll');
    }

    function checkAuthDisplay() {
        const token = localStorage.getItem('authToken');
        if (token) {
            if(userForm) userForm.classList.remove('hidden');
            if(guestMsg) guestMsg.classList.add('hidden');
        } else {
            if(userForm) userForm.classList.add('hidden');
            if(guestMsg) guestMsg.classList.remove('hidden');
        }
    }

    if (loginLink) {
        loginLink.addEventListener('click', () => {
            closeModal();
            const mainLoginBtn = document.getElementById('reg_login_id');
            if(mainLoginBtn) mainLoginBtn.click();
        });
    }

    function updateModalHeader() {
        const pageTitle = document.getElementById('country-name');
        const pageDesc = document.getElementById('country-short-desc');
        
        if (modalName && pageTitle) modalName.textContent = pageTitle.textContent;
        if (modalDesc && pageDesc) modalDesc.textContent = pageDesc.textContent;
        if (modalImg) modalImg.src = "resourses/img-country.png"; 
    }

    async function loadReviews(id) {
        if (!reviewsList) return;
        reviewsList.innerHTML = '<p style="text-align:center; padding:20px; color:#888;">Loading...</p>';
        
        try {
            const response = await fetch(`http://localhost:8888/wuide/api.php/reviews?country_id=${id}`);
            const reviews = await response.json();
            reviewsList.innerHTML = ''; 

            if (reviews.length === 0) {
                reviewsList.innerHTML = '<p style="text-align:center; padding:20px; color:#aaa;">No reviews yet.</p>';
                return;
            }

            reviews.forEach(r => {
                const photo = r.user_photo ? r.user_photo : 'resourses/logo.svg';
                const date = new Date(r.date).toLocaleDateString();
                const html = `
                    <div class="review-card-modal">
                        <img src="${photo}" alt="user">
                        <div class="review-card-content">
                            <strong>${r.user_name} <span class="review-date-small">${date}</span></strong>
                            <p>${r.text}</p>
                        </div>
                    </div>
                `;
                reviewsList.insertAdjacentHTML('beforeend', html);
            });
        } catch (e) {
            console.error(e);
            reviewsList.innerHTML = '<p style="color:red; text-align:center;">Error loading reviews</p>';
        }
    }

    if (postBtn) {
        postBtn.addEventListener('click', async () => {
            const text = textArea.value.trim();
            const token = localStorage.getItem('authToken');
            if (!text || !token) return;

            try {
                const res = await fetch('http://localhost:8888/wuide/api.php/reviews', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ country_id: countryId, text: text })
                });

                if (res.ok) {
                    textArea.value = '';
                    loadReviews(countryId);
                } else {
                    alert("Error sending review");
                }
            } catch (e) { console.error(e); }
        });
    }
}
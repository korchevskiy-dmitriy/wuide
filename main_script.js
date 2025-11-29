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
    modalTitle.textContent = 'Sign in'
});


closeBtn.addEventListener('click', () => {
    modal.classList.remove('open')
});

logInBtn.addEventListener('click', () => {
    modal.classList.add('open');
    signInForm.classList.add('hidden');
    logInForm.classList.remove('hidden');
    modalTitle.textContent = 'Log in'
});

window.addEventListener('click', (event) => {
    if (event.target === modal){
        modal.classList.remove('open');
    }
})

const filterPriceBtn = document.getElementById('filters_price_id');
const filterPriceList = document.getElementById('price-list-id');

filterPriceBtn.addEventListener('click', () => {
    filterPriceList.hidden = !filterPriceList.hidden
});

const filterRegionBtn = document.getElementById('filters_region_id');
const filterRegionList = document.getElementById('region-list-id');

filterRegionBtn.addEventListener('click', () => {
    filterRegionList.hidden = !filterRegionList.hidden
});

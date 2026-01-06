/* registration(sign up) logic */
const registrationForm = document.getElementById('signin-form');

if (registrationForm) {
    registrationForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(registrationForm);
        
        const data = Object.fromEntries(formData.entries());

        data.photo_url = ""; 

        console.log("Отправляем данные:", data); 

        try {
            const response = await fetch('http://localhost:8888/wuide/api.php/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data) 
            });

            const result = await response.json();
            console.log("Ответ сервера:", result);

            if (response.ok) {
                alert('Success! Registration complete. Please Log In.');
                
                registrationForm.reset();
                
                document.getElementById('reg_login_id').click(); 
                
            } else {
                alert('Error: ' + (result.error || result.message));
            }

        } catch (error) {
            console.error('Ошибка сети:', error);
            alert('Server error. Check console for details.');
        }
    });
}


/* log in logic*/

const loginForm = document.getElementById('login-form');

if (loginForm) {
    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(loginForm);
        const data = Object.fromEntries(formData.entries());

        console.log("Попытка входа:", data.email);

        try {
            const response = await fetch('http://localhost:8888/wuide/api.php/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log("Ответ сервера:", result);

            if (response.ok) {
                localStorage.setItem('authToken', result.token);
                localStorage.setItem('userId', result.userId);

                alert('Login successful! Welcome back.');

                window.location.reload(); 
            } else {
                alert('Login failed: ' + (result.error || result.message));
            }

        } catch (error) {
            console.error('Ошибка сети:', error);
            alert('Server error. Check console.');
        }
    });
}


/* authorization checking */

const profileModal = document.getElementById('profile-modal');
const closeProfileBtn = document.getElementById('close-profile-modal');

const pName = document.getElementById('prof-name');
const pSurname = document.getElementById('prof-surname');
const pCountry = document.getElementById('prof-country');
const pEmail = document.getElementById('prof-email');
const pPassword = document.getElementById('prof-password');
const pAvatarDiv = document.getElementById('profile-modal-avatar');
const pHeaderName = document.getElementById('profile-modal-name');
const pHeaderCountry = document.getElementById('profile-modal-country');

const updateBtn = document.getElementById('update-profile-btn');
const logoutBtn = document.getElementById('logout-profile-btn');
const deleteBtn = document.getElementById('delete-account-btn');

async function checkAuth() {
    const token = localStorage.getItem('authToken');
    if (!token) return;

    try {
        const response = await fetch('http://localhost:8888/wuide/api.php/auth/profile', {
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (response.ok) {
            const result = await response.json();
            const user = result.user;
            
            const userPhoto = user.photo_url ? user.photo_url : 'resourses/logo.svg';

            const navContainer = document.getElementById('main_reg_id');
            const navContainerFooter = document.getElementById('main_reg_id_end');

            const profileHTML = `
                <div class="nav-profile-trigger" id="open-profile-trigger">
                    <span class="nav-user-name">${user.name}</span>
                    <img class="nav-user-avatar" src="${userPhoto}" alt="Avatar">
                </div>
            `;

            if (navContainer) navContainer.innerHTML = profileHTML;
            if (navContainerFooter) navContainerFooter.innerHTML = profileHTML;

            const triggers = document.querySelectorAll('#open-profile-trigger');
            triggers.forEach(trig => {
                trig.addEventListener('click', () => {
                    openProfileModal(user);
                });
            });

        } else {
            console.log("Token expired");
            localStorage.removeItem('authToken');
        }

    } catch (error) {
        console.error('Auth Check Error:', error);
    }
}

checkAuth();


function openProfileModal(user) {
    if (!profileModal) return;

    profileModal.classList.add('open');
    document.body.classList.add('no-scroll');

    pName.value = user.name;
    pSurname.value = user.surname;
    pEmail.value = user.email;
    pCountry.value = user.country || "";
    pPassword.value = "";

    pHeaderName.textContent = user.name + " " + user.surname;
    pHeaderCountry.textContent = getCountryName(user.country);
    
    const photo = user.photo_url ? user.photo_url : 'resourses/logo.svg';
    pAvatarDiv.style.backgroundImage = `url('${photo}')`;
}

if (closeProfileBtn) {
    closeProfileBtn.addEventListener('click', () => {
        profileModal.classList.remove('open');
        document.body.classList.remove('no-scroll');
    });
}
window.addEventListener('click', (e) => {
    if (e.target === profileModal) {
        profileModal.classList.remove('open');
        document.body.classList.remove('no-scroll');
    }
});


if (updateBtn) {
    updateBtn.addEventListener('click', async () => {
        const token = localStorage.getItem('authToken');
        
        const updateData = {
            name: pName.value,
            surname: pSurname.value,
            country: pCountry.value
        };
        if (pPassword.value.trim() !== "") {
            updateData.password = pPassword.value.trim();
        }

        try {
            const res = await fetch('http://localhost:8888/wuide/api.php/auth/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(updateData)
            });

            if (res.ok) {
                alert("Profile updated successfully!");
                window.location.reload(); 
            } else {
                const err = await res.json();
                alert("Error updating: " + err.error);
            }
        } catch (e) {
            console.error(e);
            alert("Server error");
        }
    });
}


if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        if(confirm("Are you sure you want to log out?")) {
            localStorage.removeItem('authToken');
            localStorage.removeItem('userId');
            window.location.reload();
        }
    });
}

if (deleteBtn) {
    deleteBtn.addEventListener('click', () => {
        alert("Delete account feature is coming soon! Logging out for now.");
    });
}

function getCountryName(code) {
    const names = {
        'us': 'USA',
        'cz': 'Czech Republic',
        'de': 'Germany',
        'it': 'Italy',
        'fr': 'France',
        'ua': 'Ukraine'
    };
    return names[code] || 'Unknown';
}
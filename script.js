let menu = document.querySelector('#menu-btn');
let navbar = document.querySelector('.navbar');

menu.onclick = () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}

window.onscroll = () =>{
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
}
function selectTab(event) {
    // Remove 'active' class from all nav items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => item.classList.remove('active'));

    // Add 'active' class to the clicked nav item
    event.target.classList.add('active');
}
// // Open modal
// function openModal() {
//     document.getElementById('modal').style.display = 'block';
// }

// // Close modal
// function closeModal() {
//     document.getElementById('modal').style.display = 'none';
// }

// // Redirect to respective login/signup page
// function redirectTo(userType) {
//     if (userType === 'patient') {
//         window.location.href = 'patient_login.php'; // Replace with your patient login page
//     } else if (userType === 'doctor') {
//         window.location.href = 'doctor_login.php'; // Replace with your doctor login page
//     }
// }



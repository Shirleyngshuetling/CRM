document.addEventListener('DOMContentLoaded', function() {
    const profile = document.getElementById('profile');
    const profileMenu = document.getElementById('profileMenu');
    
    // Only enable profile menu functionality on mobile
    function checkScreenSize() {
        if (window.innerWidth <= 600) { // Mobile
            // Enable menu functionality
            profile.style.pointerEvents = 'auto';
            
            profile?.addEventListener('click', handleProfileClick);
            document.addEventListener('click', handleDocumentClick);
            profileMenu?.addEventListener('click', handleMenuClick);
        } else { // Desktop
            // Disable menu functionality
            profile.style.pointerEvents = 'none';
            
            profile?.removeEventListener('click', handleProfileClick);
            document.removeEventListener('click', handleDocumentClick);
            profileMenu?.removeEventListener('click', handleMenuClick);
            
            profileMenu.classList.remove('active');
        }
    }
    
    // Event handlers
    function handleProfileClick(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('active');
    }
    
    function handleDocumentClick() {
        profileMenu.classList.remove('active');
    }
    
    function handleMenuClick(e) {
        e.stopPropagation();
    }
    
    // Initial check
    checkScreenSize();
    
    // Re-check when window is resized
    window.addEventListener('resize', checkScreenSize);
});
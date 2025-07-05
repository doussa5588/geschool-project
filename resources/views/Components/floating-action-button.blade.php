@props([
    'actions' => [],
    'mainAction' => null,
    'position' => 'bottom-right'
])

<div class="floating-action-button {{ $position }}" id="fabContainer">
    @if($mainAction)
        <!-- Bouton principal simple -->
        <a href="{{ $mainAction['url'] }}" class="fab-main btn-{{ $mainAction['color'] ?? 'primary' }}" 
           data-bs-toggle="tooltip" title="{{ $mainAction['title'] }}">
            <i class="{{ $mainAction['icon'] }}"></i>
        </a>
    @else
        <!-- Bouton avec menu -->
        <div class="fab-menu">
            <!-- Actions secondaires -->
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" class="fab-action btn-{{ $action['color'] ?? 'primary' }}" 
                   data-bs-toggle="tooltip" title="{{ $action['title'] }}" data-bs-placement="left">
                    <i class="{{ $action['icon'] }}"></i>
                    <span class="fab-label">{{ $action['title'] }}</span>
                </a>
            @endforeach
            
            <!-- Bouton principal -->
            <button class="fab-main btn-primary" id="fabToggle">
                <i class="bi bi-plus-lg fab-icon-plus"></i>
                <i class="bi bi-x-lg fab-icon-close"></i>
            </button>
        </div>
    @endif
</div>

<style>
.floating-action-button {
    position: fixed;
    z-index: 1050;
}

.floating-action-button.bottom-right {
    bottom: 30px;
    right: 30px;
}

.floating-action-button.bottom-left {
    bottom: 30px;
    left: 30px;
}

.fab-menu {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.fab-main {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: none;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.fab-main:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 28px rgba(0,0,0,0.4);
    color: white;
}

.fab-main.btn-primary {
    background: linear-gradient(135deg, var(--unchk-primary), #1d4ed8);
}

.fab-main.btn-success {
    background: linear-gradient(135deg, var(--unchk-success), #047857);
}

.fab-main.btn-warning {
    background: linear-gradient(135deg, var(--unchk-warning), #b45309);
}

.fab-main.btn-info {
    background: linear-gradient(135deg, var(--unchk-info), #0e7490);
}

.fab-action {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
    text-decoration: none;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
    position: relative;
}

.fab-action.btn-primary {
    background: linear-gradient(135deg, var(--unchk-primary), #1d4ed8);
}

.fab-action.btn-success {
    background: linear-gradient(135deg, var(--unchk-success), #047857);
}

.fab-action.btn-warning {
    background: linear-gradient(135deg, var(--unchk-warning), #b45309);
}

.fab-action.btn-info {
    background: linear-gradient(135deg, var(--unchk-info), #0e7490);
}

.fab-action.btn-secondary {
    background: linear-gradient(135deg, var(--unchk-secondary), #475569);
}

.fab-action:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
    color: white;
}

.fab-label {
    position: absolute;
    right: 60px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 0.875rem;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.fab-action:hover .fab-label {
    opacity: 1;
}

.fab-menu.open .fab-action {
    opacity: 1;
    transform: translateY(0);
}

.fab-menu.open .fab-action:nth-child(1) { transition-delay: 0.1s; }
.fab-menu.open .fab-action:nth-child(2) { transition-delay: 0.15s; }
.fab-menu.open .fab-action:nth-child(3) { transition-delay: 0.2s; }
.fab-menu.open .fab-action:nth-child(4) { transition-delay: 0.25s; }
.fab-menu.open .fab-action:nth-child(5) { transition-delay: 0.3s; }

.fab-icon-plus,
.fab-icon-close {
    position: absolute;
    transition: all 0.3s ease;
}

.fab-icon-close {
    opacity: 0;
    transform: rotate(90deg);
}

.fab-menu.open .fab-icon-plus {
    opacity: 0;
    transform: rotate(-90deg);
}

.fab-menu.open .fab-icon-close {
    opacity: 1;
    transform: rotate(0deg);
}

.fab-main {
    transition: transform 0.3s ease;
}

.fab-menu.open .fab-main {
    transform: rotate(45deg);
}

/* Animation de pulsation */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.fab-main.pulse {
    animation: pulse 2s infinite;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .floating-action-button.bottom-right {
        bottom: 20px;
        right: 20px;
    }
    
    .fab-main {
        width: 56px;
        height: 56px;
        font-size: 1.3rem;
    }
    
    .fab-action {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fabToggle = document.getElementById('fabToggle');
    const fabMenu = document.querySelector('.fab-menu');
    
    if (fabToggle && fabMenu) {
        fabToggle.addEventListener('click', function() {
            fabMenu.classList.toggle('open');
        });
        
        // Fermer le menu en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!fabMenu.contains(e.target)) {
                fabMenu.classList.remove('open');
            }
        });
    }
});
</script>
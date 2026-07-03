@extends('admin.layout.app')

@section('container')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>

    .my-sidebar {
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .my-sidebar.active {
        transform: translateX(0);
    }

    .edit-sidebar {
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .edit-sidebar.active {
        transform: translateX(0);
    }

    .menu-block {
        background: #fff;
        border-radius: 12px;
        transition: 0.3s ease;
    }

    .menu-block:hover {
        background: #f9fbff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .sub-item {
        transition: 0.3s ease;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .sub-item:hover {
        background: #f5f7ff;
    }

    .sub-item.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    .disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    .active {
        opacity: 1;
    }

    /* CHECKBOX FIX */
    input[type="checkbox"] {
        appearance: auto !important;
        -webkit-appearance: checkbox !important;
        opacity: 1 !important;
        visibility: visible !important;
        cursor: pointer;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

   .submenu-container{
    max-height: 0;
    overflow: hidden;
    transition: all .3s ease;
}

.submenu-container.open{
    max-height: 500px;
}

</style>
<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.menu-checkbox').forEach(menu => {

        if(menu.checked)
        {
            toggleMenu(menu);
        }

    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.menu-checkbox').forEach(menu => {

        if(menu.checked)
        {
            toggleMenu(menu);

            let menuId = menu.dataset.menu;

            let container = document.getElementById('submenu-' + menuId);

            if(container)
            {
                container.classList.add('open');

                const arrow = document.querySelector('.arrow-' + menuId);

                if(arrow)
                {
                    arrow.classList.remove('ti-chevron-down');
                    arrow.classList.add('ti-chevron-up');
                }
            }
        }

    });

});
</script>
<div class="dashboard-main-body">

    <!-- HEADER -->
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">

        <div>

            <h1 class="fw-semibold mb-2 h5 text-primary-light">
                Configuration des menus pour :
                <span class="text-dark">
                    {{ $user->name }}
                </span>
            </h1>

            <div>

                <a href="{{ route('index.home.admin') }}"
                   class="text-secondary-light hover-text-primary hover-underline">

                    Dashboard

                </a>

                <span class="text-secondary-light">
                    / Paramètres des menus
                </span>

            </div>

        </div>

        <!-- BUTTON -->
        <a href="{{ route('add.users') }}"
           class="btn btn-primary-600 d-flex align-items-center gap-2">

            <i class="ri-add-large-line"></i>

            Ajouter un utilisateur

        </a>

    </div>

    <!-- CARD -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        @foreach($menus1 as $menu)

<div class="menu-block mb-3 p-3 border rounded">

    <!-- MENU PRINCIPAL -->
    <div class="form-check d-flex align-items-center">

        <input
            type="checkbox"
            class="form-check-input me-3 menu-checkbox"
            data-menu="{{ data_get($menu, 'id') }}"
            onchange="toggleMenu(this); saveMenu(this)"
            {{ in_array(data_get($menu, 'id'), $menuPermissions) ? 'checked' : '' }}
        >

        <label
            class="form-check-label d-flex align-items-center cursor-pointer flex-grow-1"
            onclick="toggleSubMenu('{{ data_get($menu, 'id') }}')"
        >
            <i class="{{ data_get($menu, 'icon_menu') }} me-2 text-primary"></i>

            <strong>{{ data_get($menu, 'nom_menu') }}</strong>

            <i class="ti ti-chevron-down ms-auto arrow-{{ data_get($menu, 'id') }}"></i>
        </label>

    </div>

    <!-- SOUS MENUS -->
    <div id="submenu-{{ data_get($menu, 'id') }}" class="submenu-container ms-4 mt-3">

        @foreach($sousmenus->where('menu_id', data_get($menu, 'id')) as $sub)

            <div class="form-check d-flex align-items-center mb-2 sub-item sub-{{ data_get($menu, 'id') }} disabled">

                <input
                    type="checkbox"
                    class="form-check-input me-3 sub-checkbox"
                    data-parent="{{ data_get($menu, 'id') }}"
                    data-submenu="{{ data_get($sub, 'id') }}"
                    onchange="saveSubMenu(this)"
                    {{ in_array(data_get($sub, 'id'), $sousmenuPermissions) ? 'checked' : '' }}
                >

                <label class="form-check-label d-flex align-items-center">

                    <i class="{{ data_get($sub, 'icon_sousmenu') }} me-2 text-secondary"></i>

                    <span>{{ data_get($sub, 'nom_sousmenu') }}</span>

                </label>

            </div>

        @endforeach

    </div>

</div>

@endforeach

    </div>
</div>
</div>
<script>
function toggleSubMenu(menuId)
{
    const submenu = document.getElementById('submenu-' + menuId);

    const arrow = document.querySelector('.arrow-' + menuId);

    submenu.classList.toggle('open');

    if(submenu.classList.contains('open'))
    {
        arrow.classList.remove('ti-chevron-down');
        arrow.classList.add('ti-chevron-up');
    }
    else
    {
        arrow.classList.remove('ti-chevron-up');
        arrow.classList.add('ti-chevron-down');
    }
}
</script>
<script>

    function toggleMenu(element) {

        const menuId = element.dataset.menu;

        const subMenus = document.querySelectorAll(`.sub-${menuId}`);
        const children = document.querySelectorAll(`[data-parent="${menuId}"]`);

        if (element.checked) {

            // Active les sous menus
            subMenus.forEach(sub => {

                sub.classList.remove('disabled');
                sub.classList.add('active');

            });

            // Active les checkbox enfants
            children.forEach(child => {

                child.disabled = false;

            });

        } else {

            // Désactive les sous menus
            subMenus.forEach(sub => {

                sub.classList.remove('active');
                sub.classList.add('disabled');

            });

            // Décoche + désactive les enfants
            children.forEach(child => {

                child.checked = false;
                child.disabled = true;

            });

        }

    }

</script>


<script>

function saveMenu(element)
{
    fetch("{{ route('user.menu.ajax') }}", {

        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },

        body: JSON.stringify({

            user_id: {{ $user->id }},
            menu_id: element.dataset.menu,
            sousmenu_id: null,
            checked: element.checked

        })

    })
    .then(response => response.json())
    .then(data => {

        if(data.success)
        {
            showToast(data.message);
        }

    });
}

</script>

<script>

function saveSubMenu(element)
{
    fetch("{{ route('user.menu.ajax') }}", {

        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },

        body: JSON.stringify({

            user_id: {{ $user->id }},
            menu_id: element.dataset.parent,
            sousmenu_id: element.dataset.submenu,
            checked: element.checked

        })

    })
    .then(response => response.json())
    .then(data => {

        if(data.success)
        {
            showToast(data.message);
        }

    });
}

</script>
<div id="toastMessage"
     class="position-fixed top-0 end-0 m-3 p-3 bg-success text-white rounded shadow"
     style="display:none; z-index:99999;">
</div>
<script>

function showToast(message)
{
    const toast = document.getElementById('toastMessage');

    toast.innerHTML = message;

    toast.style.display = 'block';

    setTimeout(() => {

        toast.style.display = 'none';

    }, 2500);
}

</script>

@endsection

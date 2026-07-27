@extends('layouts.app')

@section('content')
<h1 class="page-title">Create Reservation</h1>

<div class="card">
    @can('module-access', ['reservations', 'create'])
    <h3 class="section-title">Step 1: Check Available Rooms</h3>

    <form method="GET" action="{{ route('reservations.create') }}">
        <div class="grid-2">
            <div class="form-group">
                <label>Check In Date</label>
                <input type="date" name="check_in_date" value="{{ request('check_in_date') }}" required>
            </div>

            <div class="form-group">
                <label>Check Out Date</label>
                <input type="date" name="check_out_date" value="{{ request('check_out_date') }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Check Available Rooms</button>
        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to create reservations.</p>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back</a>
    @endcan
</div>

@can('module-access', ['reservations', 'create'])
@if (request('check_in_date') && request('check_out_date'))
@php
$checkedRooms = old('rooms', isset($selectedRoomId) && $selectedRoomId ? [$selectedRoomId] : []);
$guestMode = old('guest_mode', 'new');
@endphp

<form method="POST" action="{{ route('reservations.store') }}">
    @csrf

    <input type="hidden" name="check_in_date" value="{{ request('check_in_date') }}">
    <input type="hidden" name="check_out_date" value="{{ request('check_out_date') }}">

    <div class="card">
        <h3 class="section-title">Step 2: Select Available Room(s)</h3>

        @if ($availableRooms->count())
        <p class="muted" style="margin-bottom: 16px;">
            Select one or more rooms to continue.
        </p>

        <div class="room-board-grid">
            @foreach ($availableRooms as $room)
            @php
            $isChecked = in_array($room->id, $checkedRooms);
            @endphp

            <label class="room-status-card room-available selectable-room-card {{ $isChecked ? 'selected-room-card' : '' }}" style="cursor:pointer;">
                <div style="display:flex; justify-content:space-between; align-items:start; gap:10px; margin-bottom:10px;">
                    <div>
                        <h4 style="margin:0 0 6px 0;">Room {{ $room->room_number }}</h4>
                        <p class="muted" style="margin:0;">{{ $room->roomType->name }}</p>
                    </div>

                    <span class="badge badge-available">Available</span>
                </div>

                <div style="margin-bottom:12px;">
                    <p style="margin-bottom:6px;"><strong>Price:</strong> {{ number_format($room->roomType->base_price, 2) }}</p>
                    <p style="margin-bottom:6px;"><strong>Capacity:</strong> {{ $room->roomType->capacity }}</p>
                    <p style="margin-bottom:0;"><strong>Bed:</strong> {{ $room->roomType->bed_type ?? '-' }}</p>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <input
                        type="checkbox"
                        class="room-checkbox"
                        name="rooms[]"
                        value="{{ $room->id }}"
                        {{ $isChecked ? 'checked' : '' }}>
                    <span>Select this room</span>
                </div>
            </label>
            @endforeach
        </div>
        @else
        <p class="muted">No rooms available for the selected dates.</p>
        @endif
    </div>

    <div id="selected-rooms-summary" class="card" style="{{ count($checkedRooms) ? '' : 'display:none;' }}">
        <h3 class="section-title">Selected Room(s)</h3>
        <div id="selected-rooms-list" class="quick-links"></div>
    </div>

    <div id="reservation-form-section" class="card" style="{{ count($checkedRooms) ? '' : 'display:none;' }}">
        <h3 class="section-title">Step 3: Guest & Reservation Information</h3>

        <h4 style="margin-bottom: 15px; color:#1e3a5f;">Guest Information</h4>

        <div class="form-group">
            <label>Guest Option</label>
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <label style="font-weight:normal;">
                    <input type="radio" name="guest_mode" value="existing" {{ $guestMode == 'existing' ? 'checked' : '' }}>
                    Select Existing Guest
                </label>

                <label style="font-weight:normal;">
                    <input type="radio" name="guest_mode" value="new" {{ $guestMode == 'new' ? 'checked' : '' }}>
                    Add New Guest
                </label>
            </div>
        </div>

        <div id="existing-guest-box" @if($guestMode !='existing' ) style="display:none;" @endif>
            <div class="form-group">
                <label>Select Guest</label>
                <select name="guest_id">
                    <option value="">Select Guest</option>
                    @foreach ($guests as $guest)
                    <option value="{{ $guest->id }}" @selected(old('guest_id')==$guest->id)>
                        {{ $guest->first_name }} {{ $guest->last_name }}
                        @if($guest->phone) - {{ $guest->phone }} @endif
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="new-guest-box" @if($guestMode !='new' ) style="display:none;" @endif>
            <div class="grid-2">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" id="guest_first_name" name="guest[first_name]" value="{{ old('guest.first_name') }}">
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" id="guest_last_name" name="guest[last_name]" value="{{ old('guest.last_name') }}">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" id="guest_phone" name="guest[phone]" value="{{ old('guest.phone') }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="guest_email" name="guest[email]" value="{{ old('guest.email') }}">
                </div>
            </div>

            <div id="guest-suggestions-box" class="card" style="display:none; margin-top:10px; background:#fff8e1; border:1px solid #facc15;">
                <h4 style="margin-bottom:10px; color:#92400e;">Possible Existing Guests</h4>
                <div id="guest-suggestions-list"></div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>ID Type</label>
                    <input type="text" name="guest[id_type]" value="{{ old('guest.id_type') }}" placeholder="Passport / CNIC / Driving License">
                </div>

                <div class="form-group">
                    <label>ID Number</label>
                    <input type="text" name="guest[id_number]" value="{{ old('guest.id_number') }}">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Nationality</label>
                    <input type="text" name="guest[nationality]" value="{{ old('guest.nationality') }}">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="guest[address]" value="{{ old('guest.address') }}">
                </div>
            </div>
        </div>

        <hr style="margin: 20px 0; border:none; border-top:1px solid #e5e7eb;">

        <h4 style="margin-bottom: 15px; color:#1e3a5f;">Reservation Information</h4>

        <div class="grid-2">
            <div class="form-group">
                <label>Booking Source</label>
                <input type="text" name="booking_source" value="{{ old('booking_source') }}" list="booking_source_list" placeholder="Select booking source">

                <datalist id="booking_source_list">
                    <option value="walk-in">
                    <option value="website">
                    <option value="phone">
                </datalist>
            </div>

            <div class="form-group">
                <label>Adults</label>
                <input type="number" name="adults" min="1" value="{{ old('adults', 1) }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Children</label>
                <input type="number" name="children" min="0" value="{{ old('children', 0) }}">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Save Reservation</button>
        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back</a>
    </div>
</form>

<script>
    const guestModeRadios = document.querySelectorAll('input[name="guest_mode"]');
    const existingGuestBox = document.getElementById('existing-guest-box');
    const newGuestBox = document.getElementById('new-guest-box');

    function toggleGuestMode() {
        const selected = document.querySelector('input[name="guest_mode"]:checked')?.value;

        if (selected === 'existing') {
            existingGuestBox.style.display = '';
            newGuestBox.style.display = 'none';
        } else {
            existingGuestBox.style.display = 'none';
            newGuestBox.style.display = '';
        }
    }

    guestModeRadios.forEach(radio => {
        radio.addEventListener('change', toggleGuestMode);
    });

    toggleGuestMode();

    const firstNameInput = document.getElementById('guest_first_name');
    const lastNameInput = document.getElementById('guest_last_name');
    const phoneInput = document.getElementById('guest_phone');
    const emailInput = document.getElementById('guest_email');
    const suggestionBox = document.getElementById('guest-suggestions-box');
    const suggestionList = document.getElementById('guest-suggestions-list');

    let guestSearchTimeout;

    function buildSearchQuery() {
        const firstName = firstNameInput?.value?.trim() || '';
        const lastName = lastNameInput?.value?.trim() || '';
        const phone = phoneInput?.value?.trim() || '';
        const email = emailInput?.value?.trim() || '';

        if (phone.length >= 3) return phone;
        if (email.length >= 3) return email;

        if (firstName.length >= 2 && lastName.length >= 1) {
            return (firstName + ' ' + lastName).trim();
        }

        if (firstName.length >= 3) {
            return firstName;
        }

        return '';
    }

    function hideSuggestions() {
        suggestionBox.style.display = 'none';
        suggestionList.innerHTML = '';
    }

    function renderSuggestions(guests) {
        if (!guests.length) {
            hideSuggestions();
            return;
        }

        suggestionList.innerHTML = guests.map(guest => `
                    <div style="padding:10px 0; border-bottom:1px solid #e5e7eb;">
                        <div style="font-weight:bold; margin-bottom:4px;">
                            ${guest.first_name} ${guest.last_name}
                        </div>
                        <div style="font-size:13px; color:#64748b;">
                            ${guest.phone ?? '-'} ${guest.email ? ' | ' + guest.email : ''}
                        </div>
                        <button type="button"
                            class="btn btn-primary"
                            style="margin-top:8px; padding:6px 10px; font-size:12px;"
                            onclick="selectExistingGuest(${guest.id})">
                            Use This Guest
                        </button>
                    </div>
                `).join('');

        suggestionBox.style.display = 'block';
    }

    function searchGuests() {
        const query = buildSearchQuery();

        if (query.length < 2) {
            hideSuggestions();
            return;
        }

        fetch(`{{ route('guests.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                renderSuggestions(data);
            })
            .catch(() => {
                hideSuggestions();
            });
    }

    function debounceGuestSearch() {
        clearTimeout(guestSearchTimeout);
        guestSearchTimeout = setTimeout(searchGuests, 400);
    }

    window.selectExistingGuest = function(guestId) {
        const existingRadio = document.querySelector('input[name="guest_mode"][value="existing"]');
        const existingSelect = document.querySelector('select[name="guest_id"]');

        if (existingRadio && existingSelect) {
            existingRadio.checked = true;
            existingSelect.value = guestId;
            toggleGuestMode();
            hideSuggestions();
        }
    };

    [firstNameInput, lastNameInput, phoneInput, emailInput].forEach(input => {
        if (input) {
            input.addEventListener('input', debounceGuestSearch);
            input.addEventListener('focus', () => {
                if (input === lastNameInput && !lastNameInput.value.trim()) {
                    hideSuggestions();
                }
            });
        }
    });

    const roomCheckboxes = document.querySelectorAll('.room-checkbox');
    const reservationFormSection = document.getElementById('reservation-form-section');
    const selectedRoomsSummary = document.getElementById('selected-rooms-summary');
    const selectedRoomsList = document.getElementById('selected-rooms-list');

    function updateSelectedRoomsUI() {
        const checkedRooms = Array.from(roomCheckboxes).filter(cb => cb.checked);

        document.querySelectorAll('.selectable-room-card').forEach(card => {
            card.classList.remove('selected-room-card');
        });

        checkedRooms.forEach(cb => {
            cb.closest('.selectable-room-card')?.classList.add('selected-room-card');
        });

        if (checkedRooms.length > 0) {
            reservationFormSection.style.display = '';
            selectedRoomsSummary.style.display = '';

            selectedRoomsList.innerHTML = checkedRooms.map(cb => {
                const card = cb.closest('.selectable-room-card');
                const roomTitle = card.querySelector('h4')?.innerText ?? 'Room';
                const roomType = card.querySelector('.muted')?.innerText ?? '';
                return `<span class="badge badge-confirmed" style="padding:8px 12px;">${roomTitle} - ${roomType}</span>`;
            }).join('');
        } else {
            reservationFormSection.style.display = 'none';
            selectedRoomsSummary.style.display = 'none';
            selectedRoomsList.innerHTML = '';
            hideSuggestions();
        }
    }

    roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedRoomsUI);
    });

    updateSelectedRoomsUI();
</script>
@endif
@endcan
@endsection

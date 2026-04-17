<x-authentication title="Register">
    @section('form')
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <input class="form-control @error('full_name') is-invalid @enderror" 
                       type="text" 
                       name="full_name" 
                       id="full_name" 
                       value="{{ old('full_name') }}" 
                       required 
                       placeholder="Full Name" 
                       autocomplete="name">
                @error('full_name')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <input class="form-control @error('phone_number') is-invalid @enderror" 
                       type="text" 
                       name="phone_number" 
                       id="phone_number" 
                       value="{{ old('phone_number') }}" 
                       placeholder="Phone Number" 
                       autocomplete="tel">
                @error('phone_number')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gender -->
            <div class="form-group">
                <select class="form-control @error('gender') is-invalid @enderror" 
                        name="gender" 
                        id="gender">
                    <option value="" {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Role -->
            <div class="form-group">
                <input class="form-control @error('role') is-invalid @enderror" 
                       type="text" 
                       name="role" 
                       id="role" 
                       value="{{ old('role') }}" 
                       placeholder="Role">
                @error('role')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ward ID -->
            <div class="form-group">
                <input class="form-control @error('ward_id') is-invalid @enderror" 
                       type="number" 
                       name="ward_id" 
                       id="ward_id" 
                       value="{{ old('ward_id') }}" 
                       placeholder="Ward ID">
                @error('ward_id')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Region ID -->
            <div class="form-group">
                <input class="form-control @error('region_id') is-invalid @enderror" 
                       type="number" 
                       name="region_id" 
                       id="region_id" 
                       value="{{ old('region_id') }}" 
                       placeholder="Region ID">
                @error('region_id')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <input class="form-control @error('email') is-invalid @enderror" 
                       type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="Email" 
                       autocomplete="email">
                @error('email')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <input class="form-control @error('password') is-invalid @enderror" 
                       type="password" 
                       name="password" 
                       id="password" 
                       required 
                       placeholder="Password" 
                       autocomplete="new-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="form-group">
                <input class="form-control @error('password_confirmation') is-invalid @enderror" 
                       type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       required 
                       placeholder="Confirm Password" 
                       autocomplete="new-password">
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <!-- Terms Checkbox -->
            <div class="form-group">
                <div class="checkbox checkbox-success pt-1 pl-1">
                    <input id="checkbox-signup" 
                           type="checkbox" 
                           name="terms" 
                           {{ old('terms') ? 'checked' : '' }} 
                           required>
                    <label for="checkbox-signup" class="mb-0">
                        I accept <a href="#">Terms and Conditions</a>
                    </label>
                </div>
                @error('terms')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="form-group account-btn text-center mt-2">
                <div class="col-12">
                    <button class="btn width-md btn-bordered btn-danger waves-effect waves-light" 
                            type="submit">Register</button>
                </div>
            </div>
        </form>
    @endsection
</x-authentication>
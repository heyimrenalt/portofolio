@extends('layouts.admin')
@section('title', 'Edit Skill')

@php
  // Determine the current icon source for default radio selection.
  $currentSource = 'none';
  if ($skill->icon_upload) $currentSource = 'upload';
  elseif ($skill->icon_url) $currentSource = 'url';
  $defaultSource = old('icon_source', $currentSource);
@endphp

@section('content')
<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('admin.skills.update', $skill) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="form-group">
      <label>Category *</label>
      <select name="skill_category_id" class="form-control" required>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ old('skill_category_id', $skill->skill_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label>Skill Name *</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $skill->name) }}" required />
    </div>

    <div class="form-group">
      <label>Icon Source</label>
      <div style="display:flex;gap:20px;margin-top:6px;">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:400;">
          <input type="radio" name="icon_source" value="url" {{ $defaultSource === 'url' ? 'checked' : '' }} /> URL (CDN)
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:400;">
          <input type="radio" name="icon_source" value="upload" {{ $defaultSource === 'upload' ? 'checked' : '' }} /> Upload File
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:400;">
          <input type="radio" name="icon_source" value="none" {{ $defaultSource === 'none' ? 'checked' : '' }} /> No Icon
        </label>
      </div>
      @error('icon_source')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    {{-- URL panel --}}
    <div class="form-group" id="panel_url" style="display:none;">
      <label>Icon URL (CDN)</label>
      @if($skill->icon_url)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;" id="url_preview">
          <img src="{{ $skill->icon_url }}" style="width:32px;height:32px;object-fit:contain;" onerror="this.parentElement.style.display='none'" />
          <span style="font-size:0.8rem;color:#6b7280;">Current CDN icon</span>
        </div>
      @endif
      <input type="text" name="icon_url" class="form-control {{ $errors->has('icon_url') ? 'error' : '' }}"
             value="{{ old('icon_url', $skill->icon_url) }}" placeholder="https://cdn.jsdelivr.net/..." />
      <p class="form-hint">Contoh: devicon, icons8, simpleicons.</p>
      @error('icon_url')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    {{-- Upload panel --}}
    <div class="form-group" id="panel_upload" style="display:none;">
      <label>Upload Icon</label>
      @if($skill->icon_upload)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <img src="{{ asset($skill->icon_upload) }}" style="width:32px;height:32px;object-fit:contain;" />
          <span style="font-size:0.8rem;color:#6b7280;">Current upload — biarkan kosong untuk tetap pakai ini</span>
        </div>
      @endif
      <input type="file" name="icon_upload" class="form-control {{ $errors->has('icon_upload') ? 'error' : '' }}" accept="image/*" />
      <p class="form-hint">PNG / JPEG / WEBP, max 10MB. Biarkan kosong untuk mempertahankan icon saat ini.</p>
      @error('icon_upload')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    {{-- None panel --}}
    <div class="form-group" id="panel_none" style="display:none;">
      @if($skill->icon_upload || $skill->icon_url)
        <div style="background:#fef9c3;border:1px solid #fde047;color:#a16207;padding:10px 14px;border-radius:8px;font-size:0.82rem;">
          Icon saat ini akan <strong>dihapus permanen</strong> saat disimpan.
        </div>
      @endif
    </div>

    <div class="form-group">
      <label>Order</label>
      <input type="number" name="order" class="form-control" value="{{ old('order', $skill->order) }}" min="0" />
    </div>

    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn-admin btn-primary">Update</button>
      <a href="{{ route('admin.skills.index') }}" class="btn-admin btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  var radios = document.querySelectorAll('input[name="icon_source"]');
  var panels = {
    url:    document.getElementById('panel_url'),
    upload: document.getElementById('panel_upload'),
    none:   document.getElementById('panel_none'),
  };

  function sync(val) {
    Object.keys(panels).forEach(function (k) {
      if (panels[k]) panels[k].style.display = (k === val) ? '' : 'none';
    });
  }

  radios.forEach(function (r) {
    r.addEventListener('change', function () { sync(r.value); });
  });

  var checked = document.querySelector('input[name="icon_source"]:checked');
  sync(checked ? checked.value : 'none');
})();
</script>
@endpush

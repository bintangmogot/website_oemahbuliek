@props(['title'])

<div class="px-3 py-5" style="background-color: #fcecec; min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
        <x-session-status class="mb-2" />
          <h4 class="mb-4 text-center fw-bold">
            {{ $title }}
          </h4>

          {{ $slot }}

        </div>
      </div>
    </div>
  </div>
</div>

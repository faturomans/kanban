@extends('layouts.master')

@section('pageTitle', $pageTitle)

@section('main')
  <div class="form-container">
    <h1 class="form-title">{{ $pageTitle }}</h1>
    <form class="form" method="POST" action="{{ route('tasks.store') }}">
      @csrf
      <div class="form-item">
        <label>Name:</label>
        <input class="form-input" type="text" name="name" value="{{ old('name') }}">
        <!-- Menampilkan pesan error untuk name -->
        @error('name')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-item">
        <label>Detail:</label>
        <textarea class="form-text-area" name="detail">{{ old('detail') }}</textarea>
         <!-- Menampilkan pesan error untuk detail -->
        @error('detail')
            <div class="alert-danger">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-item">
        <label>Due Date:</label>
        <input class="form-input" type="date" name="due_date" value="{{ old('due_date') }}">
        <!-- Menampilkan pesan error untuk due_date -->
        @error('due_date')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-item">
        <label>Progress:</label>
        <select class="form-input" name="status">
            <option @if(old('status') == 'not_started') selected @endif value="not_started">
              Not Started
            </option>
            <option @if(old('status') == 'in_progress') selected @endif value="in_progress">
              In Progress
            </option>
            <option @if(old('status') == 'in_review') selected @endif value="in_review">
              Waiting/In Review
            </option>
            <option @if(old('status') == 'completed') selected @endif value="completed">
              Completed
            </option>
          </select>
        <!-- Menampilkan pesan error untuk status -->
        @error('status')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
      </div>
      <button type="submit" class="form-button">Submit</button>
    </form>
  </div>
@endsection

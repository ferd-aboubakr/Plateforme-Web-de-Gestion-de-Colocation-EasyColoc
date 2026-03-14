@props(['title', 'value', 'icon' => '', 'color' => 'blue'])

@php
$map = [
  'blue'   => ['bg-light-blue-50',   'border-light-blue-200',   'text-primary-700'],
  'green'  => ['bg-green-50',  'border-green-200',  'text-green-700'],
  'yellow' => ['bg-yellow-50', 'border-yellow-200', 'text-yellow-700'],
  'red'    => ['bg-red-50',    'border-red-200',    'text-red-700'],
];
[$bg, $border, $text] = $map[$color] ?? $map['blue'];
@endphp

<div class="{{ $bg }} border {{ $border }} rounded-xl p-5 flex flex-col gap-2">
  <div class="text-3xl">{{ $icon }}</div>
  <div class="text-3xl font-bold {{ $text }}">{{ $value }}</div>
  <div class="text-sm {{ $text }} opacity-75 font-medium">{{ $title }}</div>
</div>

{{ $w = isset($w) ? $w : 6 }}
{{ $h = isset($h) ? $h : 6 }}
<svg class="inline w-{{a $w }} h-{{a $h }}">
    <use xlink:href="/admin/images/heroicons/icon.svg#{{a $name }}"></use>
</svg>

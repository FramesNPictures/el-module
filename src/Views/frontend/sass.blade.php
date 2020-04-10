@if(count($definition->getCss()))
@foreach($definition->getCss() as $stylesheet)
{!! '@import "'.$definition->relative($stylesheet).'";' !!}
@endforeach
@endif

@if(count($definition->getSass()))
@foreach($definition->getSass() as $stylesheet)
{!! '@import "'.$definition->relative($stylesheet).'";' !!}
@endforeach
@endif
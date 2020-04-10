@if(count($definition->getLess()))
@foreach($definition->getLess() as $stylesheet)
    {!! '@import "'.$definition->relative($stylesheet).'";' !!}
@endforeach
@endif
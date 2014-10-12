@extends('layout')

@section('title')
News - {{{ $news->getTitle() }}}
@stop

@section('content')
  <div class="row index-wrapper thisisnews" data-equalizer>

    <div class="large-8 large-offset-2 columns news-content" >
      <h2>{{{ $news->getTitle() }}} <small>{{{ date('m/d/Y', strtotime($news->created_at)) }}}</small></h2>
      {{ $news->getBody() }}
    </div>

    <div class="large-12 columns other-news">
      <div class="small-6 columns">{{ $news->getPrev() }}</div>
      <div class="small-6 columns text-right">{{ $news->getNext() }}</div>
    </div>

    <div class="medium-12 columns">
      <div id="disqus_thread"></div>
      <br><!--lol this line break-->
      <script type="text/javascript">
        var disqus_shortname = 'vbanstatus';
        var disqus_identifier = 'news-{{{ $news->getId() }}}';
        var disqus_title = 'VacStatus News [{{{ $news->getId() }}}]';

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
      </script>
    </div>
  </div>

@stop

@extends('base')
@section('title')
&mdash; About
@show

@section('content')
  <h1>About &amp; Contact</h1>
  <div class="col-sm-8">
    <p>Maybe I'll write something here...</p>
    <p>Email : jung3o@yahoo.com (the o is a letter o as in oreo and not the number 0)</p>
  </div>
  <br>
  <br>
  <br>
  <br>
  <br>
  <div class="col-md-8 col-md-offset-2">
    <div id="disqus_thread"></div>
    <script type="text/javascript">
      var disqus_shortname = 'vbanstatus';
      var disqus_identifier = 'site news';
      var disqus_title = 'vBan Status [site news]';

      /* * * DON'T EDIT BELOW THIS LINE * * */
      (function() {
          var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
          dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
          (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
      })();
    </script>
  </div>
@stop

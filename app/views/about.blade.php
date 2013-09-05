@extends('layout')

@section('title')
	<title>About</title>
@stop

@section('content')
    <div class="page-header">
        <h1>About<small> Like My Cat?</small></h1>
      </div>
        <p>
          Need to look at something cute for 2 minutes today? <strong>Rate a cat.</strong>
        </p>
        <p>
          Need a place for your anger and you don't want to take it out on your loving family? <strong>Rate a cat.</strong>
        </p>
        <p>
          Enjoy clicking for hours on end? <strong>Please, rate all the cats.</strong>
        </p>
        <p>
          Quite simply <i>Like My Cat?</i> is a site where you get to click on pictures of cats and rate them as you see fit.
          It's not terribly complicated, certainly not insidious, and it's actually pretty fun.
        </p>

        <br/>
        <h3>FAQ:</h3>
        <div class="media">
          <div class="media-body">
            <h4 class="media-heading">Why?</h4>
            While chatting about some of the dumbest app/website concepts we'd ever come across, the idea for what we just called "CatApp" was born.
            We got that far and stopped for about a year, and then decided to make it the subject of Dave's coding practice project.
          </div>
        </div>

        <div class="media">
          <div class="media-body">
            <h4 class="media-heading">I love dogs.  I want to rate dogs.  When can I rate dogs.   ...dogs?</h4>
            We will get to dogs and other animals soon, promise.  
            The journey to pointless website production begins with a single, silent, furry step.  For now, rate some cats.
          </div>
        </div>

        <div class="media">
          <div class="media-body">
            <h4 class="media-heading">I would like to report a bug/advertise on your site/tell you something important.  Where can I do this?</h4>
            We have a Contact page.  Go on, <a href="{{ URL::to('contact') }}">establish contact</a>.
          </div>
        </div>
        <hr/>
        <h3>Have other questions?  Please send those to our <a href="mailto:kim@likemycat.com">Questions</a> inbox.</h3>
      </div>
@stop
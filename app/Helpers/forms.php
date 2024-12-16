<?php


function action_form($item,$route,$permission,$actions=['show','edit','delete'])
{
 return view('action_form',compact('item','route','permission','actions')) ;
}


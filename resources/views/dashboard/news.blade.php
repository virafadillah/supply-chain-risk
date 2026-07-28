<x-app-layout>

<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1 text-primary">
                📰 News Intelligence
            </h3>
            <p class="text-muted mb-0">
                Global Supply Chain Monitoring System
            </p>
        </div>
    </div>
</x-slot>

@php

$categories = [
    'logistics' => 'Logistics',
    'trade' => 'Trade',
    'shipping' => 'Shipping',
    'economy' => 'Economy',
];

$activeCategory = request('category');

@endphp

<style>

.news-hero{
background:linear-gradient(135deg,#0d6efd,#4da3ff);
border-radius:20px;
padding:45px;
margin-bottom:30px;
color:white;
position:relative;
overflow:hidden;
}

.news-hero::after{
content:'';
position:absolute;
width:280px;
height:280px;
background:rgba(255,255,255,.08);
border-radius:50%;
right:-90px;
top:-90px;
}

.news-hero h2{
font-weight:700;
font-size:34px;
margin-bottom:12px;
}

.news-hero p{
font-size:16px;
opacity:.95;
max-width:650px;
margin:0;
}

.filter-box{
background:#fff;
border-radius:18px;
padding:20px;
margin-bottom:30px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
}

.filter-title{
font-size:15px;
font-weight:700;
margin-bottom:15px;
color:#0d6efd;
}

.category-btn{

display:inline-block;
padding:10px 18px;
border-radius:50px;
margin-right:8px;
margin-bottom:10px;

background:#eef5ff;
color:#0d6efd;

font-weight:600;
text-decoration:none;

transition:.3s;

}

.category-btn:hover{

background:#0d6efd;
color:white;

}

.category-active{

background:#0d6efd;
color:white;

}

.news-card{

background:white;
border-radius:18px;
overflow:hidden;

box-shadow:0 5px 20px rgba(0,0,0,.08);

transition:.3s;

height:100%;

display:flex;
flex-direction:column;

}

.news-card:hover{

transform:translateY(-6px);

box-shadow:0 12px 25px rgba(13,110,253,.20);

}

.news-image{

width:100%;
height:200px;
object-fit:cover;

}

.news-body{

padding:18px;

display:flex;
flex-direction:column;

height:100%;

}

.news-country{

font-size:13px;
color:#6c757d;

margin-bottom:10px;

}

.news-title{

font-size:19px;

font-weight:700;

line-height:1.45;

margin-bottom:12px;

color:#212529;

}

.news-desc{

font-size:14px;

color:#6c757d;

line-height:1.7;

margin-bottom:18px;

}

.news-footer{

margin-top:auto;

border-top:1px solid #eee;

padding-top:15px;

}

.news-source{

font-size:13px;

color:#6c757d;

}

.news-date{

font-size:13px;

color:#6c757d;

text-align:right;

}

.read-btn{

display:block;

width:100%;

padding:11px;

background:#0d6efd;

color:white;

border-radius:10px;

font-weight:600;

text-align:center;

text-decoration:none;

transition:.3s;

margin-top:18px;

}

.read-btn:hover{

background:#0b5ed7;

color:white;

}

</style>

<div class="news-hero">

<h2>

Global Supply Chain News

</h2>

<p>

Monitor logistics, shipping, economy, and international trade news in real time.

</p>

</div>

<div class="filter-box">

<div class="filter-title">

Filter News Category

</div>

<a href="{{ route('news') }}"
class="category-btn {{ !$activeCategory ? 'category-active' : '' }}">

All

</a>

@foreach($categories as $key=>$label)

<a
href="{{ route('news',['category'=>$key]) }}"
class="category-btn {{ $activeCategory==$key ? 'category-active' : '' }}">

{{ $label }}

</a>

@endforeach

</div>

<div class="row g-4">

@forelse($news as $item)
<div class="col-xl-4 col-lg-4 col-md-6">

<div class="news-card">

@if($item->image)

<img
    src="{{ $item->image }}"
    class="news-image"
    alt="{{ $item->title }}"
    loading="lazy">

@else

<img
    src="https://picsum.photos/600/400?random={{ $item->id }}"
    class="news-image"
    alt="News Image"
    loading="lazy">

@endif

<div class="news-body">

<div class="news-country">

🌍 {{ $item->country->name ?? 'Unknown Country' }}

</div>

<div class="news-title">

{{ $item->title }}

</div>

<div class="news-desc">

{{ \Illuminate\Support\Str::limit($item->description ?? 'No description available.', 150) }}

</div>

<div class="news-footer">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="small text-muted">

📰 Source

</div>

<strong>

{{ $item->source ?? 'Unknown' }}

</strong>

</div>

<div class="text-end">

<div class="small text-muted">

📅 Published

</div>

<strong>

@if($item->published_at)

{{ $item->published_at->format('d M Y') }}

@else

-

@endif

</strong>

</div>

</div>

@if($item->url)

<a
href="{{ $item->url }}"
target="_blank"
class="read-btn">

Read Full Article →

</a>

@else

<button
class="read-btn"
disabled>

Article Unavailable

</button>

@endif

</div>

</div>

</div>

</div>
@empty

<div class="col-12">

    <div class="text-center py-5">

        <img
            src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png"
            width="120"
            class="mb-4">

        <h3 class="fw-bold text-primary">

            No News Available

        </h3>

        <p class="text-muted">

            There are currently no news articles in this category.

        </p>

        <a
            href="{{ route('news') }}"
            class="btn btn-primary rounded-pill px-4">

            View All News

        </a>

    </div>

</div>

@endforelse

</div>

@if($news->hasPages())

<div class="mt-5 d-flex justify-content-center">

    {{ $news->links() }}

</div>

@endif
<style>

@media (max-width:992px){

.news-hero{

padding:35px 25px;

}

.news-hero h2{

font-size:28px;

}

.news-card{

margin-bottom:10px;

}

.news-image{

height:180px;

}

}

@media (max-width:768px){

.news-hero{

text-align:center;

padding:30px 20px;

}

.news-hero h2{

font-size:24px;

}

.news-hero p{

font-size:14px;

}

.filter-box{

padding:15px;

}

.category-btn{

display:inline-block;

width:100%;

text-align:center;

margin-bottom:10px;

}

.news-image{

height:190px;

}

.news-title{

font-size:17px;

}

.news-desc{

font-size:13px;

}

}

.news-card{

animation:fadeUp .45s ease;

}

@keyframes fadeUp{

from{

opacity:0;

transform:translateY(20px);

}

to{

opacity:1;

transform:translateY(0);

}

}

.news-card:hover .news-image{

transform:scale(1.05);

transition:.4s;

}

.news-image{

transition:.4s;

}

.news-country i{

color:#0d6efd;

margin-right:6px;

}

</style>

</x-app-layout>
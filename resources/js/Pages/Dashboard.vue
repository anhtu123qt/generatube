<script setup>
import {ref, reactive} from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import {BASE_URL} from '@/Modules/baseUrl';
import {YOUTUBE_STANDARD_VIDEO, convertIdToUrl} from "@/helper";

const url = ref("");
let videoDetail = reactive({});
let relatedVideos = reactive({});
let isShowResult = ref(false);

const onSearchVideo = async (url) => {
    isShowResult.value = false;
    const isStandardVideo = url.includes(YOUTUBE_STANDARD_VIDEO);
    let videoId = isStandardVideo ? new URL(url).searchParams.get('v') : url.slice(url.lastIndexOf('/') + 1);
    try {
        await axios.post(BASE_URL.YOUTUBE.SEARCH.ID, {
            id: videoId,
            type_search: 'fullURL'
        })
            .then((res) => {
                videoDetail = res.data[0];
                relatedVideos = res.data.slice(1);
                isShowResult.value = res.data.length > 0;
            })
            .catch((err) => console.log(err))

    } catch (err) {
        console.log(err)
    }
}

</script>

<template>
    <Head title="Home"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Home</h2>
        </template>
        <!--SEARCH-->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form>
                        <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="search" id="searchInput"
                                   v-model="url"
                                   class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   placeholder="https://www.youtube.com/watch?v="
                            >
                            <button type="button"
                                    id="searchBtn"
                                    @click="onSearchVideo(url)"
                                    class="text-white absolute right-2.5 bottom-2.5 bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--END SEARCH-->
        <div class="flex flex-row max-w-7xl mx-auto sm:px-6 lg:px-8 gap-2" v-if="isShowResult">
            <!--VIDEO-->
            <div class="basis-2/5">
                <iframe
                    width="560" height="315"
                    :src="`https://www.youtube.com/embed/${videoDetail.id}`"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                    class="w-full text-center bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700"
                ></iframe>
            </div>
            <!--END VIDEO-->
            <!--CORE-->
            <div class="basic-3/5">
                <div
                    class="w-full text-center bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 rounded-t-lg bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:bg-gray-800"
                        id="defaultTab" data-tabs-toggle="#defaultTabContent" role="tablist">
                        <li class="mr-2">
                            <button id="download-tab" data-tabs-target="#download" type="button" role="tab"
                                    aria-controls="download" aria-selected="true"
                                    class="inline-block p-4 text-blue-600 rounded-tl-lg hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-blue-500">
                                Download
                            </button>
                        </li>
                        <li class="mr-2">
                            <button id="transcribe-tab" data-tabs-target="#transcribe" type="button" role="tab"
                                    aria-controls="transcribe" aria-selected="false"
                                    class="inline-block p-4 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                Transcribe
                            </button>
                        </li>
                    </ul>
                    <div id="defaultTabContent">
                        <div class="bg-white rounded-lg md:p-8 dark:bg-gray-800" id="download" role="tabpanel"
                             aria-labelledby="download-tab">
                            <h5 class="mb-2 text-3xl font-bold text-green-800 dark:text-white">Download Video <br>
                            </h5>
                            <h6 class="mb-2 text-3xl font-bold text-gray-600 dark:text-white">
                                {{ videoDetail.title }}
                            </h6>


                            <div class="overflow-x-auto">
                                <table class="w-full text-md text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            File Type
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            File Size
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                            v-for="(size, resolution) in videoDetail?.fileSize" key="index"
                                        >
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-500 whitespace-nowrap dark:text-white">
                                                {{ resolution }} (.mp4)
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ size.size }} MB
                                            </td>
                                            <td class="px-6 py-4">
                                                <button type="button"
                                                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                                    Download
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="transcribe"
                             role="tabpanel" aria-labelledby="transcribe-tab">
                            <h5 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">Transcribe Video <br>
                                Boost your
                                skills with ChatGPT: Creating a transcription and translation tool using OpenAI</h5>
                            <button type="button"
                                    class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Process
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--END CORE-->
        </div>

        <!--RELATED VIDEOS-->
        <div class="py-12" v-if="isShowResult">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h5 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">Related Videos</h5>
                <hr class="h-px bg-green-500 border-0 dark:bg-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                    <div
                        v-for="(video, index) in relatedVideos"
                        class="hover:pointer-events-auto"
                        @click="onSearchVideo(convertIdToUrl(video.id))"
                    >
                        <a href="#">
                            <img
                                class="h-auto max-w-full rounded-lg"
                                :src="video.thumbnail.url"
                            >
                            <h6 class="text-lg font-bold dark:text-white hover:text-gray-700">
                                {{ video.title }}
                            </h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--END RELATED VIDEO-->
    </AuthenticatedLayout>
</template>



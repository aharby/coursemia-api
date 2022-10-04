<template>

    <div style="width: 100%">


        <vue-cal
            v-show="!contentLoading"
            :disable-views="['years', 'year']"
            default-view="month"
            :events="events"
            :on-event-click="onEventClick"
            :locale="local"
            :time-from="7 * 60"
            :time-to="23 * 60"
            :time-step="45"
            events-on-month-view="short"
            class="vuecal--green-theme"
            :no-event-overlaps="true"
            @view-change="getChange"
        />
        <Loader v-if="contentLoading" class="content-loader"/>

        <!-- Using Vuetify -->
        <div data-app>
            <v-dialog v-model="showDialog" width="30%">
                <v-card>
                    <v-card-title>
                        <span>#{{ selectedEvent.sessionId }} {{ selectedEvent.title }}</span>
                        <v-spacer/>
                    </v-card-title>
                    <v-card-text>
                        <p v-html="selectedEvent.contentFull"/>
                        <ul>
                            <li>
                                {{ selectedEvent.start && selectedEvent.start.formatTime() }}
                                ---- {{ selectedEvent.end && selectedEvent.end.formatTime() }}
                            </li>
                            <li v-if="selectedEvent.isEndedByInstructor" style="color: red">
                                {{ selectedEvent.isEndedByInstructorMessage}}
                            </li>
                        </ul>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer/>
                        <a
                            v-if="selectedEvent.is_session_running"
                            class="btn btn-block ml-5 mt-0 py-2 btn-main"
                            @click="
                openSession(
                  selectedEvent.oSessionId,
                  selectedEvent.meetingType,
                  selectedEvent.joinUrl,
                  selectedEvent.token,
                  selectedEvent.baseUrl,
                  selectedEvent.local,
                )
              "
                            style=" padding: 0.875rem 0;"
                        >
                            {{ $t('goto_session') }}
                        </a>
                        <a
                            v-if="!selectedEvent.is_session_running"
                            class="btn btn-block ml-5 mt-0 py-2 disabled"
                            href="#"
                            style=" padding: 0.875rem 0;"
                        >
                            {{ selectedEvent.statusMessage }}
<!--                            {{ $t('not_available') }}-->
                        </a>
                        <a
                            class="btn btn-block mt-0 py-2 btn-main"
                            :href="selectedEvent.preparationView"
                            style="padding: 0.875rem 0;">
                            {{ $t('view_preparation') }}
                        </a>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>
</template>
<script>
    import axios from 'axios';
    import VueCal from "vue-cal"
    import "vue-cal/dist/vuecal.css"
    import "vue-cal/dist/i18n/ar.js"
    import Loader from "./Loader";
    import {Formatter} from "sarala-json-api-data-formatter"

    export default {
        components: {
            "vue-cal": VueCal,
            Loader: Loader,
        },
        data() {
            return {
                events: [],
                logs: [],
                contentLoading: true,
                showDialog:false,
                selectedEvent: {},
            }
        },
        computed: {
            local() {
                const locale=  this.$route.params.locale;

                return locale;
            },
        },

        async mounted() {
         const id=  this.$route.params.id
            this.fetchEvents(id);
            this.selectedEvent.preparationView = `/${this.$route.params.locale}/school-branch-supervisor/sessions/preparations/view/${this.selectedEvent.sessionId}`;
        },


        methods: {
            openSession(vcrSessionId, meetingType, joinUrl,token,baseApp,local) {
                console.log(`here`, meetingType, vcrSessionId, joinUrl,token,baseApp,local);
                if (meetingType === "zoom") {
                    console.log(`hereZoom`);
                    this.openZoom(vcrSessionId,token,baseApp,local);
                } else {
                    this.openAgora(joinUrl);
                }
            },
            openZoom(sessionId,token,baseApp,local) {
                console.log(sessionId, `'asd`);
                let url = `Ta3lom:${sessionId},Bearer ${token},school_session,${local},${baseApp}`;
                console.log(url)
                let test = window.open(url, "_blank");
                test.focus();
            },
            openAgora(joinUrl) {
                let test = window.open(joinUrl);
                test.focus();
            },
            getChange ({ view, startDate, endDate, week }) {
                const id = this.$route.params.id
                this.fetchEvents(id, startDate, endDate)
            },
            onEventClick(event, e){
                this.selectedEvent = event
                this.selectedEvent.preparationView = `/${this.$route.params.locale}/school-branch-supervisor/sessions/preparations/view/${this.selectedEvent.sessionId}`;
                console.log(this.selectedEvent);
                this.showDialog = true
                // Prevent navigating to narrower view (default vue-cal behavior).
                e.stopPropagation()
            },
            fetchEvents(id, startDate, endDate) {
                this.contentLoading = true;
                this.events = [];

                axios
                    .get(`/${this.$route.params.locale}/school-branch-supervisor/classrooms/${id}/timetable`, {
                        params : {
                            from: startDate,
                            to: endDate
                        }
                    })
                    .then(response => {
                        this.contentLoading = false
                        const formatter = new Formatter();
                        const logs = formatter.deserialize(response.data).data
                        this.filterEv(logs)

                    }).catch((error) => {
                    this.contentLoading = false
                        console.log({err: error})
                    });
                // this.$axios
                //     .get("student/classroom-class")
                //     .then((response) => {
                //         const logs = this.$formatter().deserialize(response.data).data
                //         this.filterEv(logs)
                //         this.contentLoading = false
                //     })
                //     .catch((error) => {
                //         console.log({ err: error })
                //         this.contentLoading = false
                //     })
            },
            filterEv(logs) {
                logs.forEach((item) => {
                    // let now = new Date();
                    // let from = new Date(item.day + " " + item.from_time);
                    // let to = new Date(item.day + " " + item.to_time);

                    //let title = `${item.subject.data.name} - ${item.instructor.data.name}`
                    //let sessionId = item.id
                    // let isSessionRunning = false;
                    // if (now >= from && now < to) {
                    //     isSessionRunning = true;
                    // }
                    let isSessionRunning=item.is_session_running;
                    let title = item.subject.data.name;
                    let start = item.day + " " + item.from_time;
                    let end = item.day + " " + item.to_time;
                    let sessionId = item.id;
                    let joinUrl = item.join_url;
                    let isEndedByInstructor = item.is_ended_by_instructor;
                    let isEndedByInstructorMessage = item.is_ended_by_instructor_message;
                    let status = item.status;
                    let meetingType = item.meeting_type;
                    let token = item.token;
                    let local = item.local;
                    let baseUrl = item.baseUrl;
                    let sessionType = item.sessionType
                    let oSessionId = item.vcr_session_id
                    /*
                    *Ta3lom:1613689,token,school_session,ar,https://ouredu.testenv.tech/api/v1
                    * */
                    this.pushEvent(start, end, title, sessionId, joinUrl, isSessionRunning, isEndedByInstructor, isEndedByInstructorMessage, status,meetingType,token,local,baseUrl,sessionType,oSessionId);
                })
            },
            pushEvent(start, end, title, sessionId, joinUrl, is_session_running, isEndedByInstructor, isEndedByInstructorMessage, status,meetingType,token,local,baseUrl,sessionType,oSessionId) {
                let event = {
                    start: start,
                    end: end,
                    title: title,
                    duration: 45,
                    sessionId: sessionId,
                    joinUrl: joinUrl,
                    is_session_running: is_session_running,
                    isEndedByInstructor: isEndedByInstructor,
                    isEndedByInstructorMessage: isEndedByInstructorMessage,
                    statusMessage: status,
                    meetingType:meetingType,
                    token:token,
                    local:local,
                    baseUrl:baseUrl,
                    sessionType:sessionType,
                    oSessionId:oSessionId
                }
                this.events.push(event)
            },
        },

    }
</script>

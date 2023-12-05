import { Component, OnInit } from '@angular/core';
import * as Highcharts from 'highcharts/highmaps';
import worldMap from "@highcharts/map-collection/custom/world.geo.json";
import { AppSettingsService } from 'src/app/app-settings.service';
import { NgxSpinnerService } from 'ngx-spinner';
import { GeneralService } from 'src/app/services/general.service';


import { AuthService } from '../auth/auth.service';
import { OwlOptions } from 'ngx-owl-carousel-o';
@Component({
  templateUrl: 'dashboard.component.html',
  styleUrls: ['dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
  userName: string;
  userProfile: string | undefined;
  cardDetails: any = [];
  userChartDetails: any = [];
  lineOptions: any;
  userCardData: any;
  volunteerCardData: any;
  donationCardData: any;
  projectCardData: any;
  userLineChart: any;
  userChartMonthSelect: any = 'week';
  userChartTypeSelect: any = 'active';
  sponsorChartDetails: any = [];
  sponsorChartData: any;
  donationChartMonthSelect: any = 'month';
  donationChartdata: any;
  donationChartLable: any = [];
  dashboardFooterDetails: any = [];
  mapChartSearchType: string = 'donation';
  mapChartValueType: string = 'normal';
  mapLabelDetails: any = [];
  mapContryDetails: any = [];
  mapChartDetails: any = [];
  mapDonationButtonColor: string = '';
  Highcharts: typeof Highcharts = Highcharts;
  chartOptions: Highcharts.Options = {};
  chartConstructor = "mapChart";
  chartData = [{ code3: "ABW", z: 105 }, { code3: "AFG", z: 35530 }];
  isMapChartShow: boolean = false;
  constructor(
    public authService: AuthService,
    private appSettingsService: AppSettingsService,
    private spinner: NgxSpinnerService,
  ) {
    this.userName = localStorage.getItem('name') ?? '';
    this.userProfile = localStorage.getItem('image') ?? '';
  }

  ProfileSlider: OwlOptions = {
    items: 4,
    loop: true,
    mouseDrag: true,
    touchDrag: true,
    pullDrag: false,
    dots: true,
    nav: false,
    navSpeed: 600,
    autoHeight: true,
    autoWidth: false,
    margin: 10,
    responsive: {
      0: {
        items: 1,
      },
      400: {
        items: 1,
      },
      760: {
        items: 1,
      },
      1000: {
        items: 3,
      },
      1500: {
        items: 4,
      }
    },
  }
  RecentRating: OwlOptions = {
    items: 4,
    loop: true,
    mouseDrag: true,
    touchDrag: true,
    pullDrag: false,
    dots: true,
    nav: false,
    navSpeed: 600,
    autoHeight: true,
    autoWidth: false,
    margin: 10,
    responsive: {
      0: {
        items: 1,
      },
      400: {
        items: 1,
      },
      760: {
        items: 1,
      },
      1000: {
        items: 3,
      },
      1500: {
        items: 4,
      }
    },
  }

  ngOnInit(): void {
    this.spinner.show();
    this.getDashboardCardDetails();
    this.getDashboardUserChartDetails();
    this.getDashboardSponserChartDetails();
    this.getDashboardFooterDetails();
    this.getMapChartDetails();

  }
  getDashboardCardDetails() {
    this.appSettingsService.getDashboardCardDetails().subscribe((res: any) => {
      if (res.success === true) {
        this.cardDetails = res.data;
        this.createCardChart();
      }
    });
  }
  // card
  createCardChart() {
    this.lineOptions = {
      maintainAspectRatio: false,
      elements: {
        line: {
          tension: 0.4
        },
        point: {
          radius: 2
        }
      },
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        x: {
          display: false
        },
        y: {
          display: false
        }
      }
    };

    this.userCardData = [
      {
        labels: this.cardDetails.users_chart.label,
        datasets: [
          {
            backgroundColor: 'transparent',
            borderColor: '#fff',
            borderWidth: 2,
            data: this.cardDetails.users_chart.data,
            pointBackgroundColor: '#417D46'
          }
        ]
      }
    ];
    this.donationCardData = [
      {
        labels: this.cardDetails.donation_chart.label,
        datasets: [
          {
            backgroundColor: 'transparent',
            borderColor: '#fff',
            borderWidth: 2,
            data: this.cardDetails.donation_chart.data,
            pointBackgroundColor: '#417D46'
          }
        ]
      }
    ];
    this.volunteerCardData = [
      {
        labels: this.cardDetails.volunteer_chart.label,
        datasets: [
          {
            backgroundColor: 'transparent',
            borderColor: '#fff',
            borderWidth: 2,
            data: this.cardDetails.volunteer_chart.data,
            pointBackgroundColor: '#417D46'
          }
        ]
      }
    ];
    this.projectCardData = [
      {
        labels: this.cardDetails.project_chart.label,
        datasets: [
          {
            backgroundColor: 'transparent',
            borderColor: '#fff',
            borderWidth: 2,
            data: this.cardDetails.project_chart.data,
            pointBackgroundColor: '#417D46'
          }
        ]
      }
    ];
  }

  //end card

  //user line chart
  getDashboardUserChartDetails() {
    this.appSettingsService.getDashboardUserChartDetails({ 'searchType': this.userChartMonthSelect, 'status': this.userChartTypeSelect }).subscribe((res: any) => {

      if (res.success === true) {
        this.userChartDetails = res.data;
        this.createUserLineChart();
      }
    });
  }

  userLineChartOptions = {
    tooltips: {
      position: 'absolute',
    },
    plugins: {
      tooltip: {
        position: 'absolute',
      },
      legend: {
        display: false
      }
    },
  };
  createUserLineChart() {
    this.userLineChart = {
      labels: this.userChartDetails.label,
      datasets: [
        {

          backgroundColor: 'rgba(220, 220, 220, 0.2)',
          borderColor: '#417D46',
          pointBackgroundColor: 'rgba(220, 220, 220, 1)',
          pointBorderColor: '#417D46',
          data: this.userChartDetails.data
        },
      ]
    };
  }

  //Hichart start for  map
  getMapChartDetails() {
    this.appSettingsService.getMapChartDetails({ 'searchType': this.mapChartSearchType, 'valueType': this.mapChartValueType }).subscribe((res: any) => {
      if (res.success === true) {
        this.mapChartDetails = res.data.response;
        if (this.mapChartSearchType == 'project') {
          this.mapLabelDetails = this.mapChartDetails.country_project_count;
          this.mapContryDetails = this.mapChartDetails.country_name_project_count;
        } else {
          this.mapLabelDetails = this.mapChartDetails.country_donation_count;
          this.mapContryDetails = this.mapChartDetails.country_name_donation_count;
        }
        this.chartOptions = {
          chart: {
            map: worldMap
          },
          credits: {
            enabled: false
          },
          tooltip: {
            useHTML: true,
            style: {
              padding: '10px',
              color: '#FFFFFF',
              border: '0px',
            },
            formatter: function () {
              // Custom tooltip content
              return '<div class="map-tooltip"">' + this.point.name + '<br>' +
                '<b>' + this.point.value + '<b>' + '</div>';
            }
          },
          // title: {
          //   text: "Highmaps basic demo"
          // },
          // subtitle: {
          //   text:
          //     'Source map: <a href="http://code.highcharts.com/mapdata/custom/world.js">World, Miller projection, medium resolution</a>'
          // },
          mapNavigation: {
            enabled: true,
            buttonOptions: {
              alignTo: "spacingBox"
            }
          },
          legend: {
            enabled: false
          },
          colorAxis: {
            min: 0,
            stops: [
              [0, '#F2F2F2']
            ]
          },

          series: [
            {
              type: "map",
              name: " ",
              states: {
                hover: {
                  color: "#417D46"
                }
              },
              allAreas: false,
              data: this.mapLabelDetails
            }
          ]
        };
        this.isMapChartShow = true;
        Highcharts.mapChart('container', this.chartOptions);
        this.spinner.hide();
      }
    });
  }
  mapDonationButtonClick(type: string) {
    this.mapChartSearchType = type;
    this.getMapChartDetails();
  }
  checkBoxChanged(event: any) {
    const isChecked = event.target.checked;
    if (isChecked) {
      this.mapChartValueType = 'percentage';
    } else {
      this.mapChartValueType = 'normal'
    }
    this.getMapChartDetails();
  }



  //sponser chart 
  getDashboardSponserChartDetails() {
    this.appSettingsService.getDashboardSponserChartDetails({ 'searchType': this.donationChartMonthSelect }).subscribe((res: any) => {

      if (res.success === true) {
        this.sponsorChartDetails = res.data;
        this.createSposerChart();
      }
    });
  }
  createSposerChart() {
    //sponsor chart
    this.sponsorChartData = {
      labels: ['Individual', 'Corporate'],
      datasets: [{
        backgroundColor: ['#56BCE3', '#BD052D'],
        data: [this.sponsorChartDetails.individualSponserCount, this.sponsorChartDetails.corpoSponserCount]
      }],
    };

    //donation chart

    if (this.donationChartMonthSelect == 'month') {
      this.donationChartLable = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    } else {
      this.donationChartLable = this.sponsorChartDetails.year;
    }
    this.donationChartdata = {
      labels: this.donationChartLable,
      datasets: [
        {
          label: 'Individual',
          backgroundColor: '#56BCE3',
          borderColor: '#56BCE3',
          pointBackgroundColor: '#56BCE3',
          pointBorderColor: '#fff',
          data: this.sponsorChartDetails.individual
        },
        {
          label: 'Corporate',
          backgroundColor: '#BD052D',
          borderColor: '#BD052D',
          pointBackgroundColor: '#BD052D',
          pointBorderColor: '#fff',
          data: this.sponsorChartDetails.corporate
        }
      ]
    };
    this.spinner.hide();
  }

  //footer info
  getDashboardFooterDetails() {
    this.appSettingsService.getDashboardFooterDetails().subscribe((res: any) => {
      if (res.success === true) {
        this.dashboardFooterDetails = res.data;
      }
    });
  }



  userTimeSpendData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'My First dataset',
        backgroundColor: 'rgba(255, 225, 225, 1)',
        borderColor: 'rgba(65, 125, 70, 1)',
        pointBackgroundColor: '#fff',
        pointBorderColor: 'rgba(65, 125, 70, 1)',
        data: [40, 20, 12, 39, 10, 80, 40]
      }
    ]
  };
}

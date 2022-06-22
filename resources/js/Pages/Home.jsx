import React from "react";
import { Link, Head } from "@inertiajs/inertia-react";

export default function Welcome({ me, top }) {
    const pageDescription =
        " Here are your top 10 tracks over the last 6 months...";

    const topMap = top.items.map((item, index) => {
        return (
            <li className="mb-3 flex flex-col" key={index}>
                {/* Track Image */}
                <div className="flex justify-center items-center">
                    <a href={item.external_urls.spotify} target="_blank">
                        <img
                            src={item.album.images[0].url}
                            alt="..."
                            className="mx-auto py-3 hover:animate-pulse drop-shadow-md"
                            style={{
                                maxWidth: "250px",
                            }}
                        />
                    </a>
                </div>
                {/* Position */}
                <p className="text-xs uppercase font-bold mb-3">{index + 1}</p>
                {/* Track Name */}
                <p className="mb-1">{item.name}</p>
                {/* Artist Name */}
                <p className="text-sm font-bold">{item.artists[0].name}</p>
            </li>
        );
    });

    return (
        <>
            <Head title={me.display_name + " Top Tracks"}></Head>
            <div className="container mx-auto">
                <div className="flex justify-center items-center py-10">
                    <div>
                        <div className="flex justify-center">
                            <img
                                style={{ height: "100px", width: "100px" }}
                                src={me.images[0].url}
                                className="rounded-full shadow-md h-full object-cover object-center"
                            ></img>
                        </div>
                        <div className="text-2xl text-center py-3">
                            Welcome,{" "}
                            <span className="font-bold">
                                {" "}
                                {me.display_name}
                            </span>
                            <img
                                src="/img/Spotify_Logo_RGB_Black.png"
                                alt="spotify"
                                className="mx-auto py-3"
                                style={{ maxWidth: "100px" }}
                            />
                        </div>

                        <div className="py-3 tracking-wider">
                            <p className="text-green-700 uppercase mb-10 text-center">
                                {pageDescription}
                            </p>
                            {/* Map Top Tracks */}
                            <ul className="text-center gap-5 grid grid-cols-2">
                                {topMap}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
